<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\AppointmentDetail;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelPdf\Facades\Pdf;

class AppointmentController extends Controller
{
    public function index(){
        $appointments = Appointment::with('client', 'appointmentDetails.service')->paginate(10);
        
        if (request()->wantsJson()) {
            if ($appointments->isEmpty()){
                return response()->json([
                    'message' => 'no se encontraron citas'
                ], 400);
            }

            return response()->json([
                'message' => 'aqui estan las citas',
                'data' => $appointments
            ], 200);
        }

        return view('appointments.index', compact('appointments'));
    }

    public function show($id){
        $appointment = Appointment::with('client', 'appointmentDetails.service')->find($id);
        
        if (request()->wantsJson() ){
            if (!$appointment){
                return response()->json([
                    'message' => 'cita no encontrada'
                ], 404);
            }

            return response()->json([
                'message' => 'aqui esta la cita',
                'data' => $appointment
            ], 200);
        }

        if (!$appointment){
            return redirect()->route('appointments.index')->with('error', 'Cita no encontrada');
        }
        return view('appointments.show', compact('appointment'));
    }

    public function showDay($chairID, $date){
        $appointments = Appointment::whereDate('startHour', $date)
        ->where('chairID', $chairID)
        ->get();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'aqui estan las citas del dia',
                'data' => $appointments
            ], 200);
        }

        return view('appointments.index', compact('appointments'));
    }

    public function store(Request $request){
        $request->validate([
            'clientID' => 'required|exists:clients,clientID',
            'employeeID' => 'required|exists:employees,employeeID',
            'chairID' => 'required|exists:chairs,chairID',
            'startHour' => 'required|date',
            'services' => 'required|array',
            'services.*.serviceID' => 'required|exists:services,serviceID',
            'services.*.totalPrice' => 'required|numeric'
        ]);

        try {
            $appointment = DB::transaction(function() use ($request) {
                $servicesIds = collect($request->services)->pluck('serviceID')->toArray();
                $uniqueServicesIds = array_unique($servicesIds);
                $totalDuration = (int) Service::whereIn('serviceID', $uniqueServicesIds)->sum('aproxDuration');

                $newStart = Carbon::parse($request->startHour);
                $newEnd = $newStart->copy()->addMinutes($totalDuration);

                // =========================================================================
                // VALIDACIÓN 1: ¿La silla seleccionada soporta TODOS los servicios pedidos?
                // =========================================================================
                $supportedServicesCount = DB::table('chair_service')
                    ->where('chairID', $request->chairID)
                    ->whereIn('serviceID', $uniqueServicesIds)
                    ->count();

                // Si el conteo en la tabla pivote no coincide con la cantidad de servicios solicitados...
                if ($supportedServicesCount !== count($uniqueServicesIds)) {
                    throw ValidationException::withMessages([
                        'chairID' => "La silla seleccionada no cuenta con el equipamiento para realizar todos los servicios solicitados."
                    ]);
                }

                // =========================================================================
                // VALIDACIÓN 2: ¿El empleado está trabajando y está libre? (Traslape Empleado)
                // =========================================================================
                $employeeOverlap = Appointment::where('employeeID', $request->employeeID)
                    ->whereDate('startHour', $newStart->toDateString())
                    ->where(function($query) use ($newStart, $newEnd) {
                        $query->where('startHour', '<', $newEnd->toDateTimeString())  // <-- Forzado a String de MySQL
                            ->where('finishHour', '>', $newStart->toDateTimeString()); // <-- Forzado a String de MySQL
                    })->exists();

                if ($employeeOverlap) {
                    throw ValidationException::withMessages([
                        'startHour' => "El empleado ya tiene otra cita en este horario."
                    ]);
                }

                // =========================================================================
                // VALIDACIÓN 3: ¿La SILLA está libre en ese rango de tiempo? (Traslape Silla)
                // =========================================================================
                $chairOverlap = Appointment::where('chairID', $request->chairID)
                    ->whereDate('startHour', $newStart->toDateString())
                    ->where(function($query) use ($newStart, $newEnd) {
                        $query->where('startHour', '<', $newEnd->toDateTimeString())  // <-- Forzado a String de MySQL
                            ->where('finishHour', '>', $newStart->toDateTimeString()); // <-- Forzado a String de MySQL
                    })->exists();

                if ($chairOverlap) {
                    throw ValidationException::withMessages([
                        'chairID' => "La silla seleccionada ya está ocupada por otra cita en este horario."
                    ]);
                }

                // =========================================================================
                // GUARDAR CITA
                // =========================================================================
                $appointment = Appointment::create([
                    'clientID' => $request->clientID,
                    'employeeID' => $request->employeeID,
                    'chairID' => $request->chairID, // Guardamos la silla
                    'startHour' => $newStart,
                    'finishHour' => $newEnd,
                    'status' => 'pending',
                ]);

                foreach ($request->services as $serviceData) {
                    AppointmentDetail::create([
                        'appointmentID' => $appointment->appointmentID,
                        'serviceID' => $serviceData['serviceID'],
                        'totalPrice' => $serviceData['totalPrice']
                    ]);
                }

                return $appointment;
            });

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Cita creada exitosamente',
                    'data' => $appointment->load('client', 'appointmentDetails.service')
                ], 201);
            }

            return redirect()->route('appointments.index')->with('success', 'Cita creada exitosamente');

        } catch (ValidationException $e) {
            if (request()->wantsJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }
    
    public function update(Request $request, $id){
        $appointment = Appointment::find($id);
        if (!$appointment) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Cita no encontrada'], 404);
            }
            return redirect()->route('appointments.index')->with('error', 'Cita no encontrada');
        }

        $request->validate([
            'clientID' => 'sometimes|exists:clients,clientID',
            'employeeID' => 'sometimes|exists:employees,employeeID',
            'chairID' => 'sometimes|exists:chairs,chairID', // <-- Silla opcional en el update
            'startHour' => 'sometimes|date',
            'services' => 'sometimes|array',
            'services.*.serviceID' => 'sometimes|exists:services,serviceID', // Corregido: servicesID -> serviceID
            'services.*.totalPrice' => 'sometimes|numeric',
            'status' => 'sometimes|in:pending,in_process,cancelled,Finished'
        ]);

        try {
            $appointment = DB::transaction(function() use ($request, $appointment) {
                
                // 1. OBTENER LOS SERVICIOS (Los nuevos del request o los que ya tenía la cita)
                if ($request->has('services')) {
                    $servicesIds = collect($request->services)->pluck('serviceID')->toArray();
                } else {
                    $servicesIds = $appointment->appointmentDetails()->pluck('serviceID')->toArray();
                }

                $uniqueServicesIds = array_unique($servicesIds);

                // 2. IDENTIFICAR QUÉ SILLA SE USARÁ (La nueva o la actual)
                $chairId = $request->get('chairID', $appointment->chairID);

                // =========================================================================
                // VALIDACIÓN 1: ¿La silla (nueva o actual) soporta los servicios de la cita?
                // =========================================================================
                $supportedServicesCount = DB::table('chair_service')
                    ->where('chairID', $chairId)
                    ->whereIn('serviceID', $uniqueServicesIds)
                    ->count();

                if ($supportedServicesCount !== count($uniqueServicesIds)) {
                    throw ValidationException::withMessages([
                        'chairID' => "La silla seleccionada no cuenta con el equipamiento para los servicios requeridos en esta cita."
                    ]);
                }

                // 3. CALCULAR EL NUEVO RANGO DE TIEMPO
                $totalDuration = (int) Service::whereIn('serviceID', $uniqueServicesIds)->sum('aproxDuration'); // Corregido: servicesID -> serviceID
                $newStart = Carbon::parse($request->get('startHour', $appointment->startHour));
                $newEnd = $newStart->copy()->addMinutes($totalDuration);
                
                $employeeId = $request->get('employeeID', $appointment->employeeID);

                // =========================================================================
                // VALIDACIÓN 2: ¿El empleado trabaja en ese horario?
                // =========================================================================
                $dayOfWeek = $newStart->dayOfWeek;
                $isWorking = DB::table('employee_schedules')
                    ->where('employeeID', $employeeId)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('start_time', '<=', $newStart->toTimeString())
                    ->where('end_time', '>=', $newEnd->toTimeString())
                    ->exists();

                if (!$isWorking) {
                    throw ValidationException::withMessages([
                        'startHour' => "El empleado no trabaja en ese horario o día de la semana."
                    ]);
                }

                // =========================================================================
                // VALIDACIÓN 3: Traslape del Empleado (Excluyendo la cita actual)
                // =========================================================================
                $employeeOverlap = Appointment::where('appointmentID', '!=', $appointment->appointmentID)
                    ->where('employeeID', $employeeId)
                    ->whereDate('startHour', $newStart->toDateString())
                    ->where(function($query) use ($newStart, $newEnd) {
                        $query->where('startHour', '<', $newEnd->toDateTimeString())
                            ->where('finishHour', '>', $newStart->toDateTimeString());
                    })->exists();

                if ($employeeOverlap) {
                    throw ValidationException::withMessages([
                        'startHour' => "El empleado seleccionado ya tiene otra cita en este horario."
                    ]);
                }

                // =========================================================================
                // VALIDACIÓN 4: Traslape de la SILLA (Excluyendo la cita actual)
                // =========================================================================
                $chairOverlap = Appointment::where('appointmentID', '!=', $appointment->appointmentID)
                    ->where('chairID', $chairId)
                    ->whereDate('startHour', $newStart->toDateString())
                    ->where(function($query) use ($newStart, $newEnd) {
                        $query->where('startHour', '<', $newEnd->toDateTimeString())
                            ->where('finishHour', '>', $newStart->toDateTimeString());
                    })->exists();

                if ($chairOverlap) {
                    throw ValidationException::withMessages([
                        'chairID' => "La silla necesaria para este servicio ya está ocupada por otra cita en este horario."
                    ]);
                }

                // =========================================================================
                // PROCESAR ACTUALIZACIÓN
                // =========================================================================
                
                // Actualizar la cita principal
                $appointment->update([
                    'clientID' => $request->get('clientID', $appointment->clientID),
                    'employeeID' => $employeeId,
                    'chairID' => $chairId,
                    'startHour' => $newStart,
                    'finishHour' => $newEnd,
                    'status' => $request->get('status', $appointment->status)
                ]);

                // Si se enviaron nuevos servicios, reemplazamos el detalle
                if ($request->has('services')) {
                    AppointmentDetail::where('appointmentID', $appointment->appointmentID)->delete();

                    foreach ($request->services as $serviceData) {
                        AppointmentDetail::create([
                            'appointmentID' => $appointment->appointmentID,
                            'serviceID' => $serviceData['serviceID'],
                            'totalPrice' => $serviceData['totalPrice']
                        ]);
                    }
                }

                return $appointment;
            });

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Cita actualizada exitosamente',
                    'data' => $appointment->load('client', 'appointmentDetails.service')
                ], 200);
            }

            return redirect()->route('appointments.index')->with('success', 'Cita actualizada exitosamente');

        } catch (ValidationException $e) {
            if (request()->wantsJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Error al actualizar: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id){
        $appointment = Appointment::find($id);

        if (request()->wantsJson() ){
            if (!$appointment){
                return response()->json([
                    'message' => 'cita no encontrada'
                ], 404);
            }
            $appointment->delete();
            return response()->json([
                'message' => 'cita eliminada exitosamente'
            ], 200);
        }

        if (!$appointment){
            return redirect()->route('appointments.index')->with('error', 'Cita no encontrada');
        }
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Cita eliminada exitosamente');
    }

    public function AlterAppointmentStatus($id, $newStatus){
        $appointment = Appointment::find($id);

        if (!$appointment){
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'cita no encontrada'
                ], 404);
            }
            return redirect()->route('appointments.index')->with('error', 'Cita no encontrada');
        }

        try {
            $appointment = DB::transaction(function() use ($newStatus, $appointment){
                if ($newStatus) {
                    $appointment->status = $newStatus;
                    $appointment->save();
                }

                return $appointment;
            });

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'cita actualizada exitosamente',
                    'data' => $appointment->load('client', 'appointmentDetails.service')
                ], 200);
            }

            return redirect()->back()->with('success', 'Estatus de la cita actualizado exitosamente');

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function invoke($filter, $date){

        if ($filter === 'day') {
            $appointments = Appointment::whereDate('startHour', $date)->with('client', 'appointmentDetails.service')
            ->get();
        }
        else if ($filter === 'month') {
            $appointments = Appointment::whereMonth('startHour', Carbon::parse($date)->month)
            ->whereYear('startHour', Carbon::parse($date)->year)
            ->with('client', 'appointmentDetails.service')
            ->get();
        }
        else if ($filter === 'year') {
            $appointments = Appointment::whereYear('startHour', Carbon::parse($date)->year)
            ->with('client', 'appointmentDetails.service')
            ->get();
        }
        else {
            return response()->json([
                'message' => 'Filtro no válido. Use "day", "month" o "year".'
            ], 400);
        }
        Pdf::view('pdf.invoice', ['appointments' => $appointments, 'filter' => $filter, 'date' => $date])->save('C:/Users/USER/OneDrive/Documents/invoice'. $filter . $date .'.pdf');
    }
}
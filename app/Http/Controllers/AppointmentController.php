<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\AppointmentDetail;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class AppointmentController extends Controller
{
    public function index(){
        $appointments = Appointment::with('client', 'appointmentDetails.service')->paginate(10);
        
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

    public function show($id){
        $appointment = Appointment::with('client', 'appointmentDetails.service')->find($id);
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

    public function store(Request $request){
        $request->validate([
            'clientID' => 'required|exists:clients,clientID',
            'employeeID' => 'required|exists:employees,employeeID',
            'startHour' => 'required|date',
            'finishHour' => 'required|date|after:startHour',
            'services' => 'required|array',
            'services.*.serviceID' => 'required|exists:services,servicesID',
            'services.*.totalPrice' => 'required|numeric'
        ]);
        try{
            return DB::transaction(function() use ($request){
                $servicesIds = collect($request->services)->pluck('serviceID')->toArray();

                $totalDuration = Service::whereIn('serviceID', $servicesIds)->sum('aproxDuration');
               
                $newStart = Carbon::parse($request->startHour);
                $newEnd = $newStart->copy()->addMinutes($totalDuration);

                $overlap = Appointment::where(function($query) use ($newStart, $newEnd){
                    $query->whereDate('startHour', $newStart->toDateString());
                })
                ->where(function($query) use ($newStart, $newEnd){
                    $query->where('startHour', '<', $newEnd)
                      ->where('finishHour', '>', $newStart);
                })
                ->exists();

                if ($overlap){
                    throw ValidationException::withMessages([
                    'startHour' => "El horario seleccionado se cruza con otra cita. Duración requerida: {$totalDuration} minutos."
                ]);
                }

                $appointment = Appointment::create([
                    'clientID' => $request->clientID,
                    'employeeID' => $request->employeeID,
                    'startHour' => $request->startHour,
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
                return response()->json([
                    'message' => 'cita creada exitosamente',
                    'data' => $appointment->load('client', 'appointmentDetails.service')
                ], 201);
                });
            } catch (\Exception $e){
                return back()->withInput()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }
    
    public function update(Request $request, $id){
        $appointment = Appointment::with('client', 'appointmentDetails.service')->find($id);
        if (!$appointment){
            return response()->json([
                'message' => 'cita no encontrada'
            ], 404);
        }
    
        $request->validate([
            'clientID' => 'sometimes|exists:clients,clientID',
            'employeeID' => 'sometimes|exists:employees,employeeID',
            'startHour' => 'sometimes|date',
            'finishHour' => 'sometimes|date',
            'services' => 'sometimes|array',
            'services.*.serviceID' => 'sometimes|exists:services,servicesID',
            'services.*.totalPrice' => 'sometimes|numeric',
            'status' => 'sometimes|in:pending,in_process,cancelled,Finished'
        ]);
        try{
            return DB::transaction(function() use ($request, $appointment){
                if ($request->has('services')) {
                    $servicesIds = collect($request->services)->pluck('serviceID')->toArray();
                } else {
                    $servicesIds = $appointment->appointmentDetails()->pluck('serviceID')->toArray();
                }

                $totalDuration = Service::whereIn('serviceID', $servicesIds)->sum('aproxDuration');
               
                $newStart = Carbon::parse($request->startHour);
                $newEnd = $newStart->copy()->addMinutes($totalDuration);

                    $overlap = Appointment::where('appointmentID', '!=', $appointment->appointmentID)
                    ->whereDate('startHour', $newStart->toDateString())
                    ->where(function($query) use ($newStart, $newEnd) {
                        $query->where('startHour', '<', $newEnd)
                              ->where('finishHour', '>', $newStart);
                    })
                    ->exists();

                if ($overlap){
                    throw ValidationException::withMessages([
                    'startHour' => "El horario seleccionado se cruza con otra cita. Duración requerida: {$totalDuration} minutos."
                ]);

                }
                if ($request->has('clientID') || $request->has('startHour') || $request->has('finishHour')) {
                    $appointment->update($request->only('clientID', 'startHour', 'finishHour'));
                }
                if ($request->has('status')) {
                    $appointment->update($request->only('status'));
                }

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
    
                return response()->json([
                    'message' => 'cita actualizada exitosamente',
                    'data' => $appointment->load('client', 'appointmentDetails.service')
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id){
        $appointment = Appointment::find($id);
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

    public function AlterAppointmentStatus($id, $newStatus){
        $appointment = Appointment::find($id);
        if (!$appointment){
            return response()->json([
                'message' => 'cita no encontrada'
            ], 404);
        }
        try {
            return DB::transaction(function() use ($newStatus, $appointment){
                if ($newStatus) {
                    $appointment->status = $newStatus;
                    $appointment->save();
                }

                return response()->json([
                    'message' => 'cita actualizada exitosamente',
                    'data' => $appointment->load('client', 'appointmentDetails.service')
                ], 200);

            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}


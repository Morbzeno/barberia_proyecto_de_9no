<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Employee;
use App\Models\AppointmentDetail;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(
            'client',
            'appointmentDetails.service'
        )->paginate(10);

        if ($appointments->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron citas'
            ], 400);
        }

        return response()->json([
            'message' => 'Aquí están las citas',
            'data' => $appointments
        ], 200);
    }


    public function show($id)
    {
        $appointment = Appointment::with(
            'client',
            'appointmentDetails.service'
        )->find($id);

        if (!$appointment) {
            return response()->json([
                'message' => 'Cita no encontrada'
            ], 404);
        }

        return response()->json([
            'message' => 'Aquí está la cita',
            'data' => $appointment
        ], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | DISPONIBILIDAD
    |--------------------------------------------------------------------------
    |
    | Devuelve las horas disponibles dependiendo de:
    |
    | - silla
    | - servicio
    | - fecha
    | - duración del servicio
    | - horario del barbero
    | - citas existentes del barbero
    | - citas existentes de la silla
    |
    */

    public function availability(Request $request)
{
    $validated = $request->validate([
        'chairID' => 'required|exists:chairs,chairID',
        'serviceIDs' => 'required|array|min:1',
        'serviceIDs.*' => 'required|exists:services,serviceID',
        'date' => 'required|date|after_or_equal:today',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Obtener servicios seleccionados
    |--------------------------------------------------------------------------
    */

    $serviceIDs = collect($validated['serviceIDs'])
        ->unique()
        ->values()
        ->toArray();

    $services = Service::whereIn('serviceID', $serviceIDs)
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Calcular duración total y precio total
    |--------------------------------------------------------------------------
    */

    $totalDuration = (int) $services->sum('aproxDuration');

    $totalPrice = (float) $services->sum('price');

    if ($totalDuration <= 0) {
        return response()->json([
            'message' => 'No se pudo calcular la duración de los servicios.',
            'available' => []
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | Verificar que la silla soporte todos los servicios
    |--------------------------------------------------------------------------
    */

    $supportedServicesCount = DB::table('chairs_services')
        ->where('chairID', $validated['chairID'])
        ->whereIn('serviceID', $serviceIDs)
        ->distinct()
        ->count('serviceID');

    if ($supportedServicesCount !== count($serviceIDs)) {
        return response()->json([
            'message' => 'La silla seleccionada no ofrece todos los servicios seleccionados.',
            'available' => []
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | Obtener únicamente barberos
    |--------------------------------------------------------------------------
    */

    $barbers = Employee::with('person')
        ->where('admin_type', 'barber')
        ->get();

    if ($barbers->isEmpty()) {
        return response()->json([
            'message' => 'No hay barberos disponibles.',
            'available' => []
        ], 200);
    }

    $date = Carbon::parse($validated['date']);

    $dayName = $date->format('l');

    $available = [];

    /*
    |--------------------------------------------------------------------------
    | Revisar cada barbero
    |--------------------------------------------------------------------------
    */

    foreach ($barbers as $barber) {

        $schedule = $barber->schedule;

        if (
            !is_array($schedule) ||
            !isset($schedule['days']) ||
            !isset($schedule['hours'])
        ) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | ¿Trabaja ese día?
        |--------------------------------------------------------------------------
        */

        if (!in_array($dayName, $schedule['days'])) {
            continue;
        }

        if (
            empty($schedule['hours']['start']) ||
            empty($schedule['hours']['end'])
        ) {
            continue;
        }

        $workStart = Carbon::parse(
            $validated['date'] . ' ' . $schedule['hours']['start']
        );

        $workEnd = Carbon::parse(
            $validated['date'] . ' ' . $schedule['hours']['end']
        );

        /*
        |--------------------------------------------------------------------------
        | Crear horarios candidatos
        |--------------------------------------------------------------------------
        |
        | Primero se generan los horarios normales cada 30 minutos.
        |
        | También agregamos las horas exactas en las que termina una cita.
        |
        */

        $candidateTimes = [];

        /*
        |--------------------------------------------------------------------------
        | Horarios normales cada 30 minutos
        |--------------------------------------------------------------------------
        */

        for (
            $slot = $workStart->copy();
            $slot->copy()->addMinutes($totalDuration)->lte($workEnd);
            $slot->addMinutes(30)
        ) {
            $candidateTimes[] = $slot->copy();
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener citas existentes de la silla
        |--------------------------------------------------------------------------
        */

        $chairAppointments = Appointment::where(
                'chairID',
                $validated['chairID']
            )
            ->where('status', '!=', 'cancelled')
            ->whereDate('startHour', $validated['date'])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Agregar la hora exacta en que se libera la silla
        |--------------------------------------------------------------------------
        */

        foreach ($chairAppointments as $appointment) {

            $finish = Carbon::parse(
                $appointment->finishHour
            );

            if (
                $finish->gte($workStart) &&
                $finish->copy()->addMinutes($totalDuration)->lte($workEnd)
            ) {
                $candidateTimes[] = $finish;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener citas existentes del barbero
        |--------------------------------------------------------------------------
        */

        $barberAppointments = Appointment::where(
                'employeeID',
                $barber->employeeID
            )
            ->where('status', '!=', 'cancelled')
            ->whereDate('startHour', $validated['date'])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Agregar la hora exacta en que se libera el barbero
        |--------------------------------------------------------------------------
        */

        foreach ($barberAppointments as $appointment) {

            $finish = Carbon::parse(
                $appointment->finishHour
            );

            if (
                $finish->gte($workStart) &&
                $finish->copy()->addMinutes($totalDuration)->lte($workEnd)
            ) {
                $candidateTimes[] = $finish;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Eliminar horarios duplicados y ordenar
        |--------------------------------------------------------------------------
        */

        $candidateTimes = collect($candidateTimes)
            ->unique(function ($time) {
                return $time->format('Y-m-d H:i:s');
            })
            ->sortBy(function ($time) {
                return $time->timestamp;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Revisar cada horario candidato
        |--------------------------------------------------------------------------
        */

        foreach ($candidateTimes as $slotStart) {

            $slotStart = $slotStart->copy();

            $slotEnd = $slotStart
                ->copy()
                ->addMinutes($totalDuration);

            /*
            |--------------------------------------------------------------------------
            | No mostrar horarios pasados
            |--------------------------------------------------------------------------
            */

            if ($slotStart->isPast()) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | No terminar después del horario laboral
            |--------------------------------------------------------------------------
            */

            if ($slotEnd->gt($workEnd)) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Verificar si la silla está ocupada
            |--------------------------------------------------------------------------
            */

            $chairBusy = Appointment::where(
                    'chairID',
                    $validated['chairID']
                )
                ->where('status', '!=', 'cancelled')
                ->where(
                    'startHour',
                    '<',
                    $slotEnd->toDateTimeString()
                )
                ->where(
                    'finishHour',
                    '>',
                    $slotStart->toDateTimeString()
                )
                ->exists();

            if ($chairBusy) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Verificar si el barbero está ocupado
            |--------------------------------------------------------------------------
            */

            $barberBusy = Appointment::where(
                    'employeeID',
                    $barber->employeeID
                )
                ->where('status', '!=', 'cancelled')
                ->where(
                    'startHour',
                    '<',
                    $slotEnd->toDateTimeString()
                )
                ->where(
                    'finishHour',
                    '>',
                    $slotStart->toDateTimeString()
                )
                ->exists();

            if ($barberBusy) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Horario disponible
            |--------------------------------------------------------------------------
            */

            $available[] = [
                'time' => $slotStart->format('H:i'),

                'startHour' => $slotStart->format(
                    'Y-m-d H:i:s'
                ),

                'finishHour' => $slotEnd->format(
                    'Y-m-d H:i:s'
                ),

                'employeeID' => $barber->employeeID,

                'employee' => $barber->person
                    ? $barber->person->name . ' ' .
                      $barber->person->last_name
                    : 'Barbero',
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Ordenar horarios
    |--------------------------------------------------------------------------
    */

    usort($available, function ($a, $b) {
        return strcmp(
            $a['startHour'],
            $b['startHour']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | Respuesta
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'date' => $validated['date'],

        'chairID' => (int) $validated['chairID'],

        'services' => $services->map(function ($service) {
            return [
                'serviceID' => $service->serviceID,
                'name' => $service->name,
                'price' => $service->price,
                'duration' => (int) $service->aproxDuration,
            ];
        })->values(),

        'totalDuration' => $totalDuration,

        'totalPrice' => $totalPrice,

        'available' => $available

    ], 200);
}

    /*
    |--------------------------------------------------------------------------
    | CREAR CITA
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Obtener usuario autenticado
        |--------------------------------------------------------------------------
        */

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Debes iniciar sesión para reservar una cita.'
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener cliente relacionado
        |--------------------------------------------------------------------------
        */

        $client = $user->client;

        if (!$client) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene un cliente asociado.'
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Validar datos
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'employeeID' => 'required|exists:employees,employeeID',
            'chairID' => 'required|exists:chairs,chairID',

            'startHour' => [
                'required',
                'date',
                'after_or_equal:now'
            ],

            'services' => 'required|array|min:1',

            'services.*.serviceID' =>
                'required|exists:services,serviceID',

            'services.*.totalPrice' =>
                'required|numeric|min:0',

            'notes' => 'nullable|string|max:1000'
        ]);

        try {

            return DB::transaction(
                function () use ($request, $client) {

                    /*
                    |--------------------------------------------------------------------------
                    | Obtener servicios
                    |--------------------------------------------------------------------------
                    */

                    $servicesIds = collect($request->services)
                        ->pluck('serviceID')
                        ->unique()
                        ->values()
                        ->toArray();

                    /*
                    |--------------------------------------------------------------------------
                    | Calcular duración total
                    |--------------------------------------------------------------------------
                    */

                    $totalDuration = (int) Service::whereIn(
                        'serviceID',
                        $servicesIds
                    )->sum('aproxDuration');

                    if ($totalDuration <= 0) {
                        throw ValidationException::withMessages([
                            'services' =>
                                'No se pudo calcular la duración de los servicios.'
                        ]);
                    }

                    $newStart = Carbon::parse(
                        $request->startHour
                    );

                    $newEnd = $newStart
                        ->copy()
                        ->addMinutes($totalDuration);


                    /*
                    |--------------------------------------------------------------------------
                    | Validar servicios de la silla
                    |--------------------------------------------------------------------------
                    */

                    $supportedServicesCount = DB::table(
                        'chairs_services'
                    )
                        ->where(
                            'chairID',
                            $request->chairID
                        )
                        ->whereIn(
                            'serviceID',
                            $servicesIds
                        )
                        ->distinct()
                        ->count('serviceID');

                    if (
                        $supportedServicesCount !==
                        count($servicesIds)
                    ) {

                        throw ValidationException::withMessages([
                            'chairID' =>
                                'La silla seleccionada no cuenta con el equipamiento para realizar todos los servicios solicitados.'
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Obtener empleado
                    |--------------------------------------------------------------------------
                    */

                    $employee = Employee::findOrFail(
                        $request->employeeID
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Solo los barberos pueden atender citas
                    |--------------------------------------------------------------------------
                    */

                    if ($employee->admin_type !== 'barber') {

                        throw ValidationException::withMessages([
                            'employeeID' =>
                                'El empleado seleccionado no es un barbero.'
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Validar horario del barbero
                    |--------------------------------------------------------------------------
                    */

                    $schedule = $employee->schedule;

                    $dayName = $newStart->format('l');

                    if (
                        !is_array($schedule) ||
                        !isset($schedule['days']) ||
                        !isset($schedule['hours'])
                    ) {

                        throw ValidationException::withMessages([
                            'employeeID' =>
                                'El barbero no tiene un horario válido.'
                        ]);
                    }

                    if (!in_array(
                        $dayName,
                        $schedule['days']
                    )) {

                        throw ValidationException::withMessages([
                            'startHour' =>
                                'El barbero no trabaja ese día.'
                        ]);
                    }

                    $workStart = Carbon::parse(
                        $newStart->toDateString() .
                        ' ' .
                        $schedule['hours']['start']
                    );

                    $workEnd = Carbon::parse(
                        $newStart->toDateString() .
                        ' ' .
                        $schedule['hours']['end']
                    );

                    if (
                        $newStart->lt($workStart) ||
                        $newEnd->gt($workEnd)
                    ) {

                        throw ValidationException::withMessages([
                            'startHour' =>
                                'La cita está fuera del horario laboral del barbero.'
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Verificar traslape del empleado
                    |--------------------------------------------------------------------------
                    */

                    $employeeOverlap = Appointment::where(
                            'employeeID',
                            $request->employeeID
                        )
                        ->where(
                            'status',
                            '!=',
                            'cancelled'
                        )
                        ->where(
                            'startHour',
                            '<',
                            $newEnd->toDateTimeString()
                        )
                        ->where(
                            'finishHour',
                            '>',
                            $newStart->toDateTimeString()
                        )
                        ->exists();

                    if ($employeeOverlap) {

                        throw ValidationException::withMessages([
                            'startHour' =>
                                'El empleado ya tiene otra cita en este horario.'
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Verificar traslape de silla
                    |--------------------------------------------------------------------------
                    */

                    $chairOverlap = Appointment::where(
                            'chairID',
                            $request->chairID
                        )
                        ->where(
                            'status',
                            '!=',
                            'cancelled'
                        )
                        ->where(
                            'startHour',
                            '<',
                            $newEnd->toDateTimeString()
                        )
                        ->where(
                            'finishHour',
                            '>',
                            $newStart->toDateTimeString()
                        )
                        ->exists();

                    if ($chairOverlap) {

                        throw ValidationException::withMessages([
                            'chairID' =>
                                'La silla seleccionada ya está ocupada por otra cita en este horario.'
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Crear cita
                    |--------------------------------------------------------------------------
                    */

                    $appointment = Appointment::create([

                        'clientID' =>
                            $client->clientID,

                        'employeeID' =>
                            $request->employeeID,

                        'chairID' =>
                            $request->chairID,

                        'startHour' =>
                            $newStart,

                        'finishHour' =>
                            $newEnd,

                        'status' =>
                            'pending',

                        'notes' =>
                            $request->notes
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | Crear detalles
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $request->services as $serviceData
                    ) {

                        AppointmentDetail::create([

                            'appointmentID' =>
                                $appointment->appointmentID,

                            'serviceID' =>
                                $serviceData['serviceID'],

                            'totalPrice' =>
                                $serviceData['totalPrice']
                        ]);
                    }


                    return response()->json([
                        'message' =>
                            'Cita creada exitosamente',

                        'data' =>
                            $appointment->load(
                                'client',
                                'employee.person',
                                'chair',
                                'appointmentDetails.service'
                            )

                    ], 201);
                }
            );

        } catch (ValidationException $e) {

            return response()->json([
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Error al crear la cita: ' .
                    $e->getMessage()

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR CITA
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'message' => 'Cita no encontrada'
            ], 404);
        }

        $request->validate([

            'clientID' =>
                'sometimes|exists:clients,clientID',

            'employeeID' =>
                'sometimes|exists:employees,employeeID',

            'chairID' =>
                'sometimes|exists:chairs,chairID',

            'startHour' =>
                'sometimes|date',

            'services' =>
                'sometimes|array|min:1',

            'services.*.serviceID' =>
                'required_with:services|exists:services,serviceID',

            'services.*.totalPrice' =>
                'required_with:services|numeric|min:0',

            'status' =>
                'sometimes|in:pending,in_process,cancelled,Finished',

            'notes' =>
                'nullable|string|max:1000'
        ]);

        try {

            return DB::transaction(
                function () use ($request, $appointment) {

                    /*
                    |--------------------------------------------------------------------------
                    | Servicios
                    |--------------------------------------------------------------------------
                    */

                    if ($request->has('services')) {

                        $servicesIds = collect(
                            $request->services
                        )
                            ->pluck('serviceID')
                            ->unique()
                            ->values()
                            ->toArray();

                    } else {

                        $servicesIds =
                            $appointment
                                ->appointmentDetails()
                                ->pluck('serviceID')
                                ->toArray();
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Silla
                    |--------------------------------------------------------------------------
                    */

                    $chairId = $request->get(
                        'chairID',
                        $appointment->chairID
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Validar silla/servicios
                    |--------------------------------------------------------------------------
                    */

                    $supportedServicesCount = DB::table(
                        'chairs_services'
                    )
                        ->where(
                            'chairID',
                            $chairId
                        )
                        ->whereIn(
                            'serviceID',
                            $servicesIds
                        )
                        ->distinct()
                        ->count('serviceID');

                    if (
                        $supportedServicesCount !==
                        count($servicesIds)
                    ) {

                        throw ValidationException::withMessages([
                            'chairID' =>
                                'La silla seleccionada no cuenta con el equipamiento para los servicios requeridos.'
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Calcular duración
                    |--------------------------------------------------------------------------
                    */

                    $totalDuration = (int) Service::whereIn(
                        'serviceID',
                        $servicesIds
                    )->sum('aproxDuration');

                    $newStart = Carbon::parse(
                        $request->get(
                            'startHour',
                            $appointment->startHour
                        )
                    );

                    $newEnd = $newStart
                        ->copy()
                        ->addMinutes($totalDuration);


                    /*
                    |--------------------------------------------------------------------------
                    | Empleado
                    |--------------------------------------------------------------------------
                    */

                    $employeeId = $request->get(
                        'employeeID',
                        $appointment->employeeID
                    );

                    $employee = Employee::findOrFail(
                        $employeeId
                    );

                    if ($employee->admin_type !== 'barber') {

                        throw ValidationException::withMessages([
                            'employeeID' =>
                                'El empleado seleccionado no es un barbero.'
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Validar horario JSON
                    |--------------------------------------------------------------------------
                    */

                    $schedule = $employee->schedule;

                    $dayName = $newStart->format('l');

                    if (
                        !is_array($schedule) ||
                        !isset($schedule['days']) ||
                        !isset($schedule['hours'])
                    ) {

                        throw ValidationException::withMessages([
                            'employeeID' =>
                                'El barbero no tiene un horario válido.'
                        ]);
                    }

                    if (!in_array(
                        $dayName,
                        $schedule['days']
                    )) {

                        throw ValidationException::withMessages([
                            'startHour' =>
                                'El barbero no trabaja ese día.'
                        ]);
                    }

                    $workStart = Carbon::parse(
                        $newStart->toDateString() .
                        ' ' .
                        $schedule['hours']['start']
                    );

                    $workEnd = Carbon::parse(
                        $newStart->toDateString() .
                        ' ' .
                        $schedule['hours']['end']
                    );

                    if (
                        $newStart->lt($workStart) ||
                        $newEnd->gt($workEnd)
                    ) {

                        throw ValidationException::withMessages([
                            'startHour' =>
                                'La cita está fuera del horario laboral del barbero.'
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Traslape empleado
                    |--------------------------------------------------------------------------
                    */

                    $employeeOverlap = Appointment::where(
                            'appointmentID',
                            '!=',
                            $appointment->appointmentID
                        )
                        ->where(
                            'employeeID',
                            $employeeId
                        )
                        ->where(
                            'status',
                            '!=',
                            'cancelled'
                        )
                        ->where(
                            'startHour',
                            '<',
                            $newEnd->toDateTimeString()
                        )
                        ->where(
                            'finishHour',
                            '>',
                            $newStart->toDateTimeString()
                        )
                        ->exists();

                    if ($employeeOverlap) {

                        throw ValidationException::withMessages([
                            'startHour' =>
                                'El empleado seleccionado ya tiene otra cita en este horario.'
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Traslape silla
                    |--------------------------------------------------------------------------
                    */

                    $chairOverlap = Appointment::where(
                            'appointmentID',
                            '!=',
                            $appointment->appointmentID
                        )
                        ->where(
                            'chairID',
                            $chairId
                        )
                        ->where(
                            'status',
                            '!=',
                            'cancelled'
                        )
                        ->where(
                            'startHour',
                            '<',
                            $newEnd->toDateTimeString()
                        )
                        ->where(
                            'finishHour',
                            '>',
                            $newStart->toDateTimeString()
                        )
                        ->exists();

                    if ($chairOverlap) {

                        throw ValidationException::withMessages([
                            'chairID' =>
                                'La silla seleccionada ya está ocupada por otra cita en este horario.'
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Actualizar cita
                    |--------------------------------------------------------------------------
                    */

                    $appointment->update([

                        'clientID' =>
                            $request->get(
                                'clientID',
                                $appointment->clientID
                            ),

                        'employeeID' =>
                            $employeeId,

                        'chairID' =>
                            $chairId,

                        'startHour' =>
                            $newStart,

                        'finishHour' =>
                            $newEnd,

                        'status' =>
                            $request->get(
                                'status',
                                $appointment->status
                            ),

                        'notes' =>
                            $request->get(
                                'notes',
                                $appointment->notes
                            )
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | Reemplazar servicios
                    |--------------------------------------------------------------------------
                    */

                    if ($request->has('services')) {

                        AppointmentDetail::where(
                            'appointmentID',
                            $appointment->appointmentID
                        )->delete();

                        foreach (
                            $request->services as $serviceData
                        ) {

                            AppointmentDetail::create([

                                'appointmentID' =>
                                    $appointment->appointmentID,

                                'serviceID' =>
                                    $serviceData['serviceID'],

                                'totalPrice' =>
                                    $serviceData['totalPrice']
                            ]);
                        }
                    }


                    return response()->json([

                        'message' =>
                            'Cita actualizada exitosamente',

                        'data' =>
                            $appointment->fresh()->load(
                                'client',
                                'employee.person',
                                'chair',
                                'appointmentDetails.service'
                            )

                    ], 200);
                }
            );

        } catch (ValidationException $e) {

            return response()->json([
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Error al actualizar: ' .
                    $e->getMessage()
            ], 500);
        }
    }
    /*
    |--------------------------------------------------------------------------
    | ELIMINAR CITA
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'message' => 'Cita no encontrada'
            ], 404);
        }

        $appointment->delete();

        return response()->json([
            'message' => 'Cita eliminada exitosamente'
        ], 200);
    }
    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO
    |--------------------------------------------------------------------------
    */
    public function AlterAppointmentStatus(
        $id,
        $newStatus
    ) {
        $allowedStatuses = [
            'pending',
            'in_process',
            'cancelled',
            'Finished'
        ];

        if (!in_array($newStatus, $allowedStatuses)) {

            return response()->json([
                'message' =>
                    'Estado de cita no válido.'
            ], 422);
        }

        $appointment = Appointment::find($id);

        if (!$appointment) {

            return response()->json([
                'message' => 'Cita no encontrada'
            ], 404);
        }

        try {

            return DB::transaction(
                function () use (
                    $newStatus,
                    $appointment
                ) {

                    $appointment->status = $newStatus;

                    $appointment->save();

                    return response()->json([

                        'message' =>
                            'Cita actualizada exitosamente',

                        'data' =>
                            $appointment->load(
                                'client',
                                'appointmentDetails.service'
                            )

                    ], 200);
                }
            );

        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function myAppointments()
{
    // Obtener usuario autenticado
    $user = Auth::guard('web')->user();
    
    if (!$user) {
        return redirect()
            ->route('login')
            ->with(
                'error',
                'Debes iniciar sesión para ver tus citas.'
            );
    }

    // Obtener cliente asociado
    $client = $user->client;

    if (!$client) {
        return redirect()
            ->route('home')
            ->with(
                'error',
                'No se encontró un perfil de cliente.'
            );
    }

// Obtener las citas del cliente
$appointments = Appointment::with([
    'employee.person',
    'chair',
    'appointmentDetails.service',
    'payment'
])
    ->where('clientID', $client->clientID)
    ->orderBy('startHour', 'desc')
    ->get();

return view(
    'client.appointments.index',
    compact('appointments')
);
}
}
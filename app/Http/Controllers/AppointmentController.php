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

    public function showDailyAppointments($date, $employeeID = null){
        $query = Appointment::whereDate('startHour', $date);

        if ($employeeID) {
            $query->where('chairID', $employeeID);
        }

        $appointments = $query->with('client', 'appointmentDetails.service')->get();

        return response()->json([
            'message' => 'Aquí están las citas del día',
            'data' => $appointments
        ], 200);
    }

    public function availability(Request $request)
    {
        $validated = $request->validate([
            'chairID' => 'required|exists:chairs,chairID',
            'serviceIDs' => 'required|array|min:1',
            'serviceIDs.*' => 'required|exists:services,serviceID',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $serviceIDs = collect($validated['serviceIDs'])->unique()->values()->toArray();
        $services = Service::whereIn('serviceID', $serviceIDs)->get();
        $totalDuration = (int) $services->sum('aproxDuration');
        $totalPrice = (float) $services->sum('price');

        if ($totalDuration <= 0) {
            return response()->json([
                'message' => 'No se pudo calcular la duración de los servicios.',
                'available' => []
            ], 422);
        }

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

        $barbers = Employee::with('person')->where('admin_type', 'barber')->get();
        if ($barbers->isEmpty()) {
            return response()->json(['message' => 'No hay barberos disponibles.', 'available' => []], 200);
        }

        $date = Carbon::parse($validated['date']);
        $dayName = $date->format('l');
        $available = [];

        foreach ($barbers as $barber) {
            $schedule = $barber->schedule;
            if (!is_array($schedule) || !isset($schedule['days']) || !isset($schedule['hours'])) continue;
            if (!in_array($dayName, $schedule['days'])) continue;
            if (empty($schedule['hours']['start']) || empty($schedule['hours']['end'])) continue;

            $workStart = Carbon::parse($validated['date'] . ' ' . $schedule['hours']['start']);
            $workEnd = Carbon::parse($validated['date'] . ' ' . $schedule['hours']['end']);

            $candidateTimes = [];
            for ($slot = $workStart->copy(); $slot->copy()->addMinutes($totalDuration)->lte($workEnd); $slot->addMinutes(30)) {
                $candidateTimes[] = $slot->copy();
            }

            $chairAppointments = Appointment::where('chairID', $validated['chairID'])
                ->where('status', '!=', 'cancelled')->whereDate('startHour', $validated['date'])->get();
            foreach ($chairAppointments as $appointment) {
                $finish = Carbon::parse($appointment->finishHour);
                if ($finish->gte($workStart) && $finish->copy()->addMinutes($totalDuration)->lte($workEnd)) $candidateTimes[] = $finish;
            }

            $barberAppointments = Appointment::where('employeeID', $barber->employeeID)
                ->where('status', '!=', 'cancelled')->whereDate('startHour', $validated['date'])->get();
            foreach ($barberAppointments as $appointment) {
                $finish = Carbon::parse($appointment->finishHour);
                if ($finish->gte($workStart) && $finish->copy()->addMinutes($totalDuration)->lte($workEnd)) $candidateTimes[] = $finish;
            }

            $candidateTimes = collect($candidateTimes)->unique(fn($t) => $time->format('Y-m-d H:i:s'))->sortBy(fn($t) => $t->timestamp)->values();

            foreach ($candidateTimes as $slotStart) {
                $slotEnd = $slotStart->copy()->addMinutes($totalDuration);
                if ($slotStart->isPast() || $slotEnd->gt($workEnd)) continue;

                $chairBusy = Appointment::where('chairID', $validated['chairID'])->where('status', '!=', 'cancelled')
                    ->where('startHour', '<', $slotEnd->toDateTimeString())->where('finishHour', '>', $slotStart->toDateTimeString())->exists();
                if ($chairBusy) continue;

                $barberBusy = Appointment::where('employeeID', $barber->employeeID)->where('status', '!=', 'cancelled')
                    ->where('startHour', '<', $slotEnd->toDateTimeString())->where('finishHour', '>', $slotStart->toDateTimeString())->exists();
                if ($barberBusy) continue;

                $available[] = [
                    'time' => $slotStart->format('H:i'),
                    'startHour' => $slotStart->format('Y-m-d H:i:s'),
                    'finishHour' => $slotEnd->format('Y-m-d H:i:s'),
                    'employeeID' => $barber->employeeID,
                    'employee' => $barber->person ? $barber->person->name . ' ' . $barber->person->last_name : 'Barbero',
                ];
            }
        }

        usort($available, fn($a, $b) => strcmp($a['startHour'], $b['startHour']));

        return response()->json([
            'date' => $validated['date'],
            'chairID' => (int) $validated['chairID'],
            'services' => $services->map(fn($s) => ['serviceID' => $s->serviceID, 'name' => $s->name, 'price' => $s->price, 'duration' => (int) $s->aproxDuration])->values(),
            'totalDuration' => $totalDuration,
            'totalPrice' => $totalPrice,
            'available' => $available
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->client) return response()->json(['message' => 'Cliente no autenticado'], 403);
        $client = $user->client;

        $request->validate([
            'employeeID' => 'required|exists:employees,employeeID',
            'chairID' => 'required|exists:chairs,chairID',
            'startHour' => ['required', 'date', 'after_or_equal:now'],
            'services' => 'required|array|min:1',
            'services.*.serviceID' => 'required|exists:services,serviceID',
            'services.*.totalPrice' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            return DB::transaction(function () use ($request, $client) {
                $servicesIds = collect($request->services)->pluck('serviceID')->unique()->values()->toArray();
                $totalDuration = (int) Service::whereIn('serviceID', $servicesIds)->sum('aproxDuration');
                if ($totalDuration <= 0) throw ValidationException::withMessages(['services' => 'Duración inválida']);

                $newStart = Carbon::parse($request->startHour);
                $newEnd = $newStart->copy()->addMinutes($totalDuration);

             $appointment = Appointment::create([
    'clientID' => $client->clientID,
    'employeeID' => $request->employeeID,
    'chairID' => $request->chairID,
    'startHour' => $newStart,
    'finishHour' => $newEnd,
    'status' => 'pending',
    'notes' => $request->notes ?? 'none' /*modifique el none Jair*/
]);

                foreach ($request->services as $serviceData) {
                    AppointmentDetail::create([
                        'appointmentID' => $appointment->appointmentID,
                        'serviceID' => $serviceData['serviceID'],
                        'totalPrice' => $serviceData['totalPrice']
                    ]);
                }

                return response()->json(['message' => 'Cita creada exitosamente', 'data' => $appointment->load('client', 'employee.person', 'chair', 'appointmentDetails.service')], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear la cita: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id);
        if (!$appointment) return response()->json(['message' => 'Cita no encontrada'], 404);

        $appointment->update($request->all());
        return response()->json(['message' => 'Cita actualizada', 'data' => $appointment->fresh()], 200);
    }

    public function destroy($id)
    {
        $appointment = Appointment::find($id);
        if (!$appointment) return response()->json(['message' => 'Cita no encontrada'], 404);
        $appointment->delete();
        return response()->json(['message' => 'Cita eliminada'], 200);
    }

    public function AlterAppointmentStatus($id, $newStatus) {
        $appointment = Appointment::find($id);
        if (!$appointment) return response()->json(['message' => 'Cita no encontrada'], 404);
        $appointment->status = $newStatus;
        $appointment->save();
        return response()->json(['message' => 'Estado actualizado', 'data' => $appointment->load('client', 'appointmentDetails.service')], 200);
    }

    public function showClient(Request $request)
    {
        $clientID = $request->query('clientID');
        if (!$clientID) {
            return response()->json(['message' => 'clientID es requerido'], 400);
        }

        $appointments = Appointment::with(['client', 'appointmentDetails.service'])
            ->where('clientID', $clientID)
            ->whereIn('status', ['pending', 'in_process'])
            ->orderBy('startHour', 'asc')
            ->get();

        return response()->json($appointments);
    }
}

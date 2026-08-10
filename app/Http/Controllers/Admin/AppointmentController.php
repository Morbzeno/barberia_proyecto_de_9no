<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Chair;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $appointments = Appointment::with(['client.person', 'employee.person', 'chair'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('startHour', $request->date))
            ->orderBy('startHour', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.appointments.index', compact('appointments'));
    }

    public function create()
    {
        return view('admin.appointments.create', $this->formOptions());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Appointment::create($data);

        return redirect()->route('admin.appointments.index')->with('success', 'Cita creada correctamente.');
    }

    public function edit(Appointment $appointment)
    {
        $appointment->load(['client.person', 'employee.person', 'chair']);

        return view('admin.appointments.edit', array_merge(['appointment' => $appointment], $this->formOptions()));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $this->validated($request);

        $appointment->update($data);

        return redirect()->route('admin.appointments.index')->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('admin.appointments.index')->with('success', 'Cita eliminada correctamente.');
    }

    private function formOptions(): array
    {
        return [
            'clients' => Client::with('person')->get(),
            'employees' => Employee::with('person')->get(),
            'chairs' => Chair::orderBy('chairName')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'clientID' => 'required|exists:clients,clientID',
            'employeeID' => 'required|exists:employees,employeeID',
            'chairID' => 'required|exists:chairs,chairID',
            'startHour' => 'required|date',
            'finishHour' => 'required|date|after:startHour',
            'status' => 'required|in:pending,in_process,cancelled,Finished',
            'notes' => 'nullable|string',
        ]);
    }
}

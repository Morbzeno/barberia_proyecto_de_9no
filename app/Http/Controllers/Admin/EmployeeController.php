<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    private array $days = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

    public function index(Request $request)
    {
        $employees = Employee::with(['user', 'person'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->whereHas('person', fn ($q) => $q->where('name', 'like', '%'.$request->q.'%')
                    ->orWhere('last_name', 'like', '%'.$request->q.'%'));
            })
            ->orderBy('employeeID', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create', ['days' => $this->days]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:10',
            'rfc' => 'required|string|max:13|unique:employees,rfc',
            'payment' => 'required|numeric|min:0',
            'admin_type' => 'required|in:barber,admin,receptionist',
            'days' => 'nullable|array',
            'start_time' => 'nullable|array',
            'end_time' => 'nullable|array',
        ]);

        DB::transaction(function () use ($data, $request) {
            $user = User::create([
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

            $person = Person::create([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'phone_number' => $data['phone_number'],
            ]);

            Employee::create([
                'userID' => $user->userID,
                'personID' => $person->personID,
                'payment' => $data['payment'],
                'rfc' => $data['rfc'],
                'admin_type' => $data['admin_type'],
                'schedule' => $this->buildSchedule($request),
            ]);
        });

        return redirect()->route('admin.employees.index')->with('success', 'Empleado creado correctamente.');
    }

    public function edit(Employee $employee)
    {
        $employee->load(['user', 'person']);

        return view('admin.employees.edit', ['employee' => $employee, 'days' => $this->days]);
    }

    public function update(Request $request, Employee $employee)
    {
        $employee->load(['user', 'person']);

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($employee->userID, 'userID')],
            'password' => 'nullable|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:10',
            'rfc' => ['required', 'string', 'max:13', Rule::unique('employees', 'rfc')->ignore($employee->employeeID, 'employeeID')],
            'payment' => 'required|numeric|min:0',
            'admin_type' => 'required|in:barber,admin,receptionist',
            'days' => 'nullable|array',
            'start_time' => 'nullable|array',
            'end_time' => 'nullable|array',
        ]);

        DB::transaction(function () use ($data, $employee, $request) {
            $userData = ['email' => $data['email']];
            if (!empty($data['password'])) {
                $userData['password'] = bcrypt($data['password']);
            }
            $employee->user->update($userData);

            $employee->person->update([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'phone_number' => $data['phone_number'],
            ]);

            $employee->update([
                'payment' => $data['payment'],
                'rfc' => $data['rfc'],
                'admin_type' => $data['admin_type'],
                'schedule' => $this->buildSchedule($request),
            ]);
        });

        return redirect()->route('admin.employees.index')->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->appointments()->exists()) {
            return back()->with('error', 'No puedes eliminar un empleado con citas asociadas.');
        }

        DB::transaction(function () use ($employee) {
            $employee->delete();
            $employee->user?->delete();
            $employee->person?->delete();
        });

        return redirect()->route('admin.employees.index')->with('success', 'Empleado eliminado correctamente.');
    }

    private function buildSchedule(Request $request): array
    {
        $schedule = [];
        foreach ($request->input('days', []) as $day) {
            $schedule[] = [
                'day' => $day,
                'start' => $request->input('start_time.'.$day, '09:00'),
                'end' => $request->input('end_time.'.$day, '18:00'),
            ];
        }

        return $schedule;
    }
}

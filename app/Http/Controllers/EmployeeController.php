<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user', 'person'])->paginate(10);

        if (request()->wantsJson()) {
            if ($employees->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron empleados.'
                ], 404);
            }

            return response()->json([
                'message' => 'Todos los empleados aquí',
                'data'    => $employees
            ], 200);
        }

        if ($employees->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron empleados.');
        }

        return view('employees.index', compact('employees'));
    }

    public function show($id)
    {
        $employee = Employee::with(['user', 'person'])->find($id);

        if (request()->wantsJson()) {
            if (!$employee) {
                return response()->json([
                    'message' => 'Empleado no encontrado.'
                ], 404);
            }

            return response()->json([
                'message' => 'Datos del empleado',
                'data'    => $employee
            ], 200);
        }

        if (!$employee) {
            return redirect()->back()->with('error', 'Empleado no encontrado.');
        }

        return view('employees.show', compact('employee'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // user
            'email'        => 'required|string|email|unique:users,email|max:255',
            'password'     => 'required|string|min:8|confirmed|max:255',
            // person
            'name'         => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'phone_number' => 'required|string|max:10',
            // employee
            'rfc'          => 'required|string|unique:employees,rfc|max:13',
            'payment'      => 'required|numeric|min:0|max:10000',
            'schedule'     => 'required|array',
            'admin_type'   => 'required|in:barber,admin',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'email'    => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $person = Person::create([
                'name'         => $request->name,
                'last_name'    => $request->last_name,
                'phone_number' => $request->phone_number,
            ]);

            $employee = Employee::create([
                'userID'     => $user->userID ?? $user->id,
                'personID'   => $person->personID ?? $person->id,
                'payment'    => $request->payment,
                'rfc'        => $request->rfc,
                'schedule'   => $request->schedule,
                'workerType' => $request->workerType,
            ]);

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Empleado creado correctamente',
                    'data'    => $employee->load(['user', 'person'])
                ], 201);
            }

            return redirect()->back()->with('success', 'Empleado creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al crear el empleado: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Error al crear el empleado: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::with(['user', 'person'])->find($id);

        if (!$employee) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Empleado no encontrado'
                ], 404);
            }
            return redirect()->back()->with('error', 'Empleado no encontrado.');
        }

        $userId = $employee->user ? ($employee->user->userID ?? $employee->user->id) : null;
        $employeeId = $employee->employeeID ?? $employee->id;

        $request->validate([
            // user
            'email' => [
                'sometimes', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($userId, 'userID'),
            ],
            'password'     => 'sometimes|string|min:8|confirmed|max:255',
            // person
            'name'         => 'sometimes|string|max:255',
            'last_name'    => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:10',
            // employee
            'rfc' => [
                'sometimes', 'string', 'max:13',
                Rule::unique('employees', 'rfc')->ignore($employeeId, 'employeeID'),
            ],
            'payment'      => 'sometimes|numeric|min:0|max:10000',
            'schedule'     => 'sometimes|array',
            'workerType'   => 'sometimes|in:barbero,admin,recepcionista',
        ]);

        try {
            DB::beginTransaction();

            if ($employee->user) {
                $userData = $request->only(['email']);
                if ($request->filled('password')) {
                    $userData['password'] = bcrypt($request->password);
                }
                if (!empty($userData)) {
                    $employee->user->update($userData);
                }
            }

            if ($employee->person) {
                $employee->person->update($request->only(['name', 'last_name', 'phone_number']));
            }

            $employee->update($request->only(['payment', 'schedule', 'workerType', 'rfc']));

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Empleado actualizado correctamente',
                    'data'    => $employee->fresh(['user', 'person'])
                ], 200);
            }

            return redirect()->back()->with('success', 'Empleado actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al actualizar el empleado: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Error al actualizar el empleado: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $employee = Employee::with(['user', 'person'])->find($id);

        if (!$employee) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Empleado no encontrado'
                ], 404);
            }
            return redirect()->back()->with('error', 'Empleado no encontrado.');
        }

        try {
            DB::beginTransaction();

            $user = $employee->user;
            $person = $employee->person;

            // Eliminar el empleado primero
            $employee->delete();

            // Eliminar los registros asociados en personas y usuarios
            if ($person) {
                $person->delete();
            }
            if ($user) {
                $user->delete();
            }

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Empleado eliminado correctamente'
                ], 200);
            }

            return redirect()->back()->with('success', 'Empleado eliminado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al eliminar el empleado: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al eliminar el empleado: ' . $e->getMessage());
        }
    }
    public function barbers()
{
    $barbers = Employee::with('person')
        ->where('admin_type', 'barber')
        ->get();

    return response()->json([
        'message' => $barbers->isEmpty()
            ? 'No se encontraron barberos.'
            : 'Barberos obtenidos correctamente.',
        'data' => $barbers
    ], 200);
}
}
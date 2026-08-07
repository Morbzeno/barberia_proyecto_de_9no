<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(){
        $employees = Employee::with(['user','person'])->paginate(10);
        if($employees->isEmpty()){
            return response()->json(['message' => 'no se encontraron Employees'], 400);
        }
        return response()->json(['message' => 'Todos los Employees aquí', 'data' => $employees], 200);
    }

    public function show($id){
        $employee = Employee::with(['user', 'person',])->find($id);
        if (!$employee) return response()->json(['message' => 'Employee no encontrado'], 400);
        return response()->json(['message' => 'datos del Employee', 'data' => $employee], 200);
    }

    public function store(Request $request){
        $request->validate([
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed|max:255',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'rfc' => 'required|string|unique:employees,rfc|max:13',
            'phone_number' => 'required|string|max:10',
            'payment' => 'required|decimal:2|max:10000.00',
            'schedule' => 'required|array',
            'admin_type' => 'required|in:barber,admin,receptionist',
        ]);

        try {
            return DB::transaction(function () use ($request){
                $user = User::create(['email' => $request->email, 'password' => bcrypt($request->password)]);
                $person = Person::create(['name' => $request->name, 'last_name' => $request->last_name, 'phone_number' => $request->phone_number]);
                $employee = Employee::create([
                    'userID' => $user->userID,
                    'personID' => $person->personID,
                    'payment' => $request->payment,
                    'rfc' => $request->rfc,
                    'schedule' => $request->schedule,
                    'admin_type' => $request->admin_type,
                ]);
                return response()->json(['message' => 'Empleado creado correctamente', 'data' => $employee], 200);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id){
        $employee = Employee::with(['user', 'person'])->find($id);
        if (!$employee) return response()->json(['message' => 'Empleado no encontrado'], 404);

        $request->validate([
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($employee->userID, 'userID')],
            'password' => 'sometimes|string|min:8|confirmed|max:255',
            'name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:10',
            'payment' => 'sometimes|max:10000.00',
            'schedule' => 'sometimes|array',
            'admin_type' => 'sometimes|in:barber,admin,receptionist',
        ]);

        try {
            return DB::transaction(function () use ($request, $employee) {
                if ($request->filled('email')) $employee->user->update(['email' => $request->email]);
                if ($request->filled('password')) $employee->user->update(['password' => bcrypt($request->password)]);
                $employee->person->update($request->only(['name', 'last_name', 'phone_number']));
                $employee->update($request->only(['payment', 'schedule', 'admin_type']));
                return response()->json(['message' => 'Empleado actualizado correctamente', 'data' => $employee->fresh(['user', 'person'])], 200);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id){
        $employee = Employee::find($id);
        if(!$employee) return response()->json(['message' => 'Employee no encontrado'], 404);
        $employee->delete();
        return response()->json(['message' => 'Employee eliminado correctamente'], 200);
    }

    public function barbers(){
        $barbers = Employee::with('person')->where('admin_type', 'barber')->get();
        return response()->json(['message' => 'Barberos obtenidos', 'data' => $barbers], 200);
    }
}

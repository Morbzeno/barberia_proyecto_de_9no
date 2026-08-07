<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\Employee;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    public function store(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas.'], 401);
        }

        $role = 'client';
        $adminType = null;
        $clientId = 0;
        $employeeId = 0;
        $realName = $user->email;

        $employee = Employee::where('userID', $user->userID)->with('person')->first();
        if ($employee) {
            $role = 'employee';
            $adminType = $employee->admin_type;
            $employeeId = $employee->employeeID;
            $realName = $employee->person->name ?? 'Empleado';
        } else {
            $client = Client::where('userID', $user->userID)->with('person')->first();
            if ($client) {
                $role = 'client';
                $clientId = $client->clientID;
                $realName = $client->person->name ?? 'Cliente';
            }
        }

        $token = $user->createToken('barberia-api-token')->plainTextToken;

        // Log para depuración en el servidor
        Log::info("Login successful for User ID: {$user->userID}, Role: $role, Client ID: $clientId");

        return response()->json([
            'message' => 'OK',
            'user' => [
                'userID' => (int)$user->userID,
                'name' => $realName,
                'email' => $user->email,
                'role' => $role,
                'admin_type' => $adminType,
                'clientID' => (int)$clientId,
                'employeeID' => (int)$employeeId,
                'clientId' => (int)$clientId, // Doble para evitar fallos de casing
                'id' => (int)$clientId // Fallback extra
            ],
            'token' => $token,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Iniciar sesión desde la API.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        // Soporte para email o nombre (Búsqueda unificada)
        $user = User::with(['employee.person', 'client.person'])
            ->where('email', $request->email)
            ->orWhereHas('client.person', function($q) use ($request) {
                $q->where('name', $request->email);
            })
            ->orWhereHas('employee.person', function($q) use ($request) {
                $q->where('name', $request->email);
            })
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Las credenciales proporcionadas son incorrectas.',
            ], 401);
        }

        $role = null;
        $adminType = null;
        $clientId = null;
        $employeeId = null;
        $personId = null;
        $realName = 'Usuario';

        if ($user->employee) {
            $role = 'employee';
            $adminType = $user->employee->admin_type;
            $employeeId = $user->employee->employeeID;
            $personId = $user->employee->personID;
            $realName = $user->employee->person->name ?? 'Empleado';
        } elseif ($user->client) {
            $role = 'client';
            $clientId = $user->client->clientID;
            $personId = $user->client->personID;
            $realName = $user->client->person->name ?? 'Cliente';
        }

        if ($role === null) {
            return response()->json([
                'message' => 'El usuario no tiene un perfil asociado.',
            ], 403);
        }

        $token = $user->createToken('barberia-api-token')->plainTextToken;

        $userData = array_merge(
            $user->toArray(),
            [
                'name' => $realName,
                'role' => $role,
                'admin_type' => $adminType,
                'clientID' => $clientId,
                'employeeID' => $employeeId,
            ]
        );

        return response()->json([
            'message' => 'Inicio de sesión correcto.',
            'user' => $userData,
            'token' => $token,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Cerrar sesión.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->currentAccessToken()->delete();
        }

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        }

        return response()->json(['message' => 'Logged out'], 200);
    }
}

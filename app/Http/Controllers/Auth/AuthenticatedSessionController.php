<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    public function store(LoginRequest $request)
    {
        // Buscamos por email o por nombre de persona
        $user = User::where('email', $request->email)
            ->orWhereHas('client.person', function($q) use ($request) {
                $q->where('name', $request->email);
            })
            ->orWhereHas('employee.person', function($q) use ($request) {
                $q->where('name', $request->email);
            })
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect'
            ], 401);
        }

        // Cargar relaciones para el nombre real y rol
        $user->load(['client.person', 'employee.person']);

        $realName = 'Usuario';
        $role = 'client';
        $adminType = null;

        if ($user->employee) {
            $realName = $user->employee->person->name ?? 'Empleado';
            $role = 'employee';
            $adminType = $user->employee->admin_type;
        } elseif ($user->client) {
            $realName = $user->client->person->name ?? 'Cliente';
            $role = 'client';
        }

        // Generar token para la App
        $token = $user->createToken('barberia-api-token')->plainTextToken;

        return response()->json([
            "user" => [
                "userID" => $user->userID,
                "name" => $realName,
                "email" => $user->email,
                "role" => $role,
                "admin_type" => $adminType
            ],
            "token" => $token
        ]);
    }

    public function destroy(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
        return response()->json(['message' => 'Logged out'], 200);
    }
}

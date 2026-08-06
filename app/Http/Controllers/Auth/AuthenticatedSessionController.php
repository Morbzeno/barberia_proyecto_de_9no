<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Iniciar sesión desde la API.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ]);

        $user = User::with([
            'employee',
            'client',
        ])
            ->where('email', $request->email)
            ->first();

        if (
            !$user ||
            !Hash::check($request->password, $user->password)
        ) {
            return response()->json([
                'message' => 'Las credenciales proporcionadas son incorrectas.',
            ], 401);
        }

        $role = null;
        $adminType = null;
        $clientId = null;
        $employeeId = null;
        $personId = null;

        /*
        |--------------------------------------------------------------------------
        | Determinar el tipo de usuario
        |--------------------------------------------------------------------------
        */

       if ($user->employee) {
    $role = 'employee';
    $adminType = $user->employee->admin_type;
    $employeeId = $user->employee->employeeID;
    $personId = $user->employee->personID;
} elseif ($user->client) {
    $role = 'client';
    $clientId = $user->client->clientID;
    $personId = $user->client->personID;
}

        if ($role === null) {
            return response()->json([
                'message' => 'El usuario no tiene un perfil asociado.',
            ], 403);
        }

        $person = $personId !== null
            ? Person::find($personId)
            : null;

        /*
        |--------------------------------------------------------------------------
        | Crear token Sanctum
        |--------------------------------------------------------------------------
        */

        $token = $user->createToken(
            'barberia-api-token'
        );

        /*
        |--------------------------------------------------------------------------
        | Construir respuesta
        |--------------------------------------------------------------------------
        */

        $userData = array_merge(
            $user->toArray(),
            [
                'name' => $person ? $person->name : null,
                'last_name' => $person ? $person->last_name : null,
                'phone_number' => $person ? $person->phone_number : null,
                'role' => $role,
                'admin_type' => $adminType,
                'clientID' => $clientId,
                'employeeID' => $employeeId,
            ]
        );

        return response()->json([
            'message' => 'Inicio de sesión correcto.',
            'user' => $userData,
            'token' => $token->plainTextToken,
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Cerrar sesión.
     */
    public function destroy(Request $request): Response
    {
       $user = $request->user();

            if ($user) {
    $token = $user->currentAccessToken();

            if ($token) {
        $token->delete();
    }
}

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        }

        return response()->noContent();
    }
}
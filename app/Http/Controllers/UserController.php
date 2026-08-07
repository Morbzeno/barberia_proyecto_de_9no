<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();
        $user->load(['client.person', 'employee.person']);

        $data = null;
        if ($user->client) {
            $data = $user->client;
        } elseif ($user->employee) {
            $data = $user->employee;
        }

        return response()->json([
            "message" => "Perfil obtenido correctamente.",
            "data" => $data
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        // Lógica para actualizar perfil si se requiere
        return response()->json(["message" => "Perfil actualizado"]);
    }
}

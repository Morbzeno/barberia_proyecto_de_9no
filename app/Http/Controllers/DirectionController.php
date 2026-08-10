<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectionController extends Controller
{
    // Obtener direcciones del usuario autenticado
    public function index()
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Debes iniciar sesión.'
            ], 401);
        }

        $directions = Direction::where('userID', $user->userID)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $directions
        ], 200);
    }


    // Crear una nueva dirección
    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Debes iniciar sesión.'
            ], 401);
        }

        $request->validate([
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'residence' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $direction = Direction::create([
            'userID' => $user->userID,
            'state' => $request->state,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'name' => $request->name,
            'residence' => $request->residence,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Dirección guardada correctamente.',
            'data' => $direction
        ], 201);
    }
}
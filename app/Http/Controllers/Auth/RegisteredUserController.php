<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Person;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 1. Crear Usuario
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                // 2. Crear Persona
                $person = Person::create([
                    'name' => $request->name,
                    'last_name' => '',
                    'phone_number' => $request->phone ?? '',
                ]);

                // 3. Crear Cliente vinculado
                Client::create([
                    'userID' => $user->userID,
                    'personID' => $person->personID,
                ]);

                return response()->json($user, 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al registrar: ' . $e->getMessage()], 500);
        }
    }
}

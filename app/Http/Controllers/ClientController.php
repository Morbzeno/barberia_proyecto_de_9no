<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Mostrar todos los clientes.
     */
    public function index()
    {
        $Clients = Client::with(['user', 'person'])->paginate(10);

        if ($Clients->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron Clients',
            ], 400);
        }

        return response()->json([
            'message' => 'Todos los Clients aquí',
            'data' => $Clients
        ], 200);
    }

    /**
     * Mostrar un cliente.
     */
    public function show($id)
    {
        $Client = Client::with(['user', 'person'])->find($id);

        if (!$Client) {
            return response()->json([
                'message' => 'Client no encontrado',
            ], 404);
        }

        return response()->json([
            'message' => 'Datos del Client',
            'data' => $Client
        ], 200);
    }

    /**
     * Crear un nuevo cliente.
     */
    public function store(Request $request)
    {
        $request->validate([
            // User
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed|max:255',

            // Person
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:10',
        ]);

        try {

            DB::transaction(function () use ($request) {

                // Crear usuario
                $user = User::create([
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                // Crear información personal
                $person = Person::create([
                    'name' => $request->name,
                    'last_name' => $request->last_name,
                    'phone_number' => $request->phone_number,
                ]);

                // Crear cliente y relacionarlo con User y Person
                Client::create([
                    'userID' => $user->userID,
                    'personID' => $person->personID,
                ]);
            });

            /*
             * IMPORTANTE:
             * Después de completar correctamente la transacción,
             * mandamos al usuario al login.
             */
            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Cuenta creada correctamente. Ya puedes iniciar sesión.'
                );

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Error al crear la cuenta: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Actualizar un cliente.
     */
    public function update(Request $request, $id)
    {
        $Client = Client::with(['user', 'person'])->find($id);

        if (!$Client) {
            return response()->json([
                'message' => 'Client no encontrado'
            ], 404);
        }

        $request->validate([
            // User
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($Client->userID, 'userID'),
            ],

            'password' => 'sometimes|string|min:8|confirmed|max:255',

            // Person
            'name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',

            'rfc' => [
                'sometimes',
                'string',
                'max:13',
                Rule::unique('persons', 'rfc')
                    ->ignore($Client->personID, 'personID'),
            ],

            'phone_number' => 'sometimes|string|max:10',
        ]);

        try {

            return DB::transaction(function () use ($request, $Client) {

                // Datos del usuario
                $userData = $request->only(['email']);

                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $Client->user->update($userData);

                // Datos personales
                $Client->person->update(
                    $request->only([
                        'name',
                        'last_name',
                        'rfc',
                        'phone_number'
                    ])
                );

                return response()->json([
                    'message' => 'Client actualizado correctamente',
                    'data' => $Client->fresh(['user', 'person'])
                ], 200);
            });

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error al actualizar el Client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar cliente.
     */
    public function destroy($id)
    {
        $Client = Client::find($id);

        if (!$Client) {
            return response()->json([
                'message' => 'Client no encontrado'
            ], 404);
        }

        try {

            $Client->delete();

            return response()->json([
                'message' => 'Client eliminado correctamente'
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error al eliminar el Client',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
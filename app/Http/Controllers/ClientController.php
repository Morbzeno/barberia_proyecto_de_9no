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
        $clients = Client::with([
            'user',
            'person'
        ])->paginate(10);

        if ($clients->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron Clients',
            ], 400);
        }

        return response()->json([
            'message' => 'Todos los Clients aquí',
            'data' => $clients
        ], 200);
    }

    /**
     * Mostrar un cliente.
     */
    public function show($id)
    {
        $client = Client::with([
            'user',
            'person'
        ])->find($id);

        if (!$client) {
            return response()->json([
                'message' => 'Client no encontrado',
            ], 404);
        }

        return response()->json([
            'message' => 'Datos del Client',
            'data' => $client
        ], 200);
    }

    /**
     * Crear un nuevo cliente.
     */
    public function store(Request $request)
    {
        $request->validate([
            // User
            'email' => [
                'required',
                'string',
                'email',
                'unique:users,email',
                'max:255'
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'max:255'
            ],

            // Person
            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'last_name' => [
                'required',
                'string',
                'max:255'
            ],

            'phone_number' => [
                'required',
                'string',
                'max:10'
            ],
        ]);

        try {

            DB::transaction(function () use ($request) {

                $user = User::create([
                    'email' => $request->email,
                    'password' => Hash::make(
                        $request->password
                    ),
                ]);

                $person = Person::create([
                    'name' => $request->name,
                    'last_name' => $request->last_name,
                    'phone_number' => $request->phone_number,
                ]);

                Client::create([
                    'userID' => $user->userID,
                    'personID' => $person->personID,
                ]);
            });

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
                    'error' =>
                        'Error al crear la cuenta: ' .
                        $e->getMessage()
                ]);
        }
    }

    /**
     * Actualizar un cliente.
     *
     * IMPORTANTE:
     * Este método se conserva para web/admin.
     */
    public function update(Request $request, $id)
    {
        $client = Client::with([
            'user',
            'person'
        ])->find($id);

        if (!$client) {
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
                    ->ignore(
                        $client->userID,
                        'userID'
                    ),
            ],

            'password' => [
                'sometimes',
                'string',
                'min:8',
                'confirmed',
                'max:255'
            ],

            // Person
            'name' => [
                'sometimes',
                'string',
                'max:255'
            ],

            'last_name' => [
                'sometimes',
                'string',
                'max:255'
            ],

            'rfc' => [
                'sometimes',
                'string',
                'max:13',
                Rule::unique('persons', 'rfc')
                    ->ignore(
                        $client->personID,
                        'personID'
                    ),
            ],

            'phone_number' => [
                'sometimes',
                'string',
                'max:10'
            ],
        ]);

        try {

            return DB::transaction(
                function () use ($request, $client) {

                    // Datos del usuario
                    $userData = $request->only([
                        'email'
                    ]);

                    if ($request->filled('password')) {
                        $userData['password'] =
                            Hash::make(
                                $request->password
                            );
                    }

                    if (!empty($userData)) {
                        $client->user->update(
                            $userData
                        );
                    }

                    // Datos personales
                    $personData = $request->only([
                        'name',
                        'last_name',
                        'rfc',
                        'phone_number'
                    ]);

                    if (!empty($personData)) {
                        $client->person->update(
                            $personData
                        );
                    }

                    return response()->json([
                        'message' =>
                            'Client actualizado correctamente',

                        'data' =>
                            $client->fresh([
                                'user',
                                'person'
                            ])
                    ], 200);
                }
            );

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Error al actualizar el Client',

                'error' =>
                    $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar el perfil DEL CLIENTE AUTENTICADO.
     *
     * Este método es para Android.
     * No recibe clientID.
     * Usa el token para determinar qué cliente está conectado.
     */
    public function updateMyProfile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        $client = Client::with([
            'user',
            'person'
        ])
            ->where(
                'userID',
                $user->userID
            )
            ->first();

        if (!$client) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        $request->validate([
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore(
                        $client->userID,
                        'userID'
                    ),
            ],

            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255'
            ],

            'last_name' => [
                'sometimes',
                'required',
                'string',
                'max:255'
            ],

            'phone_number' => [
                'sometimes',
                'required',
                'string',
                'max:10'
            ],
        ]);

        try {

            return DB::transaction(
                function () use ($request, $client) {

                    /*
                     * Correo
                     */
                    if ($request->has('email')) {

                        $client->user->update([
                            'email' =>
                                $request->email
                        ]);
                    }

                    /*
                     * Información personal
                     */
                    $personData =
                        $request->only([
                            'name',
                            'last_name',
                            'phone_number'
                        ]);

                    if (!empty($personData)) {

                        $client->person->update(
                            $personData
                        );
                    }

                    $updatedClient =
                        $client->fresh([
                            'user',
                            'person'
                        ]);

                    return response()->json([
                        'message' =>
                            'Perfil actualizado correctamente',

                        'data' =>
                            $updatedClient
                    ], 200);
                }
            );

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Error al actualizar el perfil',

                'error' =>
                    $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar cliente.
     */
    public function destroy($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'message' =>
                    'Client no encontrado'
            ], 404);
        }

        try {

            $client->delete();

            return response()->json([
                'message' =>
                    'Client eliminado correctamente'
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Error al eliminar el Client',

                'error' =>
                    $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el perfil del cliente autenticado.
     *
     * Se utiliza desde Android para Editar Perfil.
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' =>
                    'Usuario no autenticado'
            ], 401);
        }

        $client = Client::with([
            'user',
            'person'
        ])
            ->where(
                'userID',
                $user->userID
            )
            ->first();

        if (!$client) {
            return response()->json([
                'message' =>
                    'Cliente no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => 'success',

            'data' => [

                /*
                 * Los dejamos también por compatibilidad
                 * con código anterior.
                 */
                'clientId' =>
                    $client->clientID,

                'name' =>
    $client->person
        ? $client->person->name
        : null,

'email' =>
    $client->user
        ? $client->user->email
        : null,

                /*
                 * Estructura completa para Android.
                 */
                'clientID' =>
                    $client->clientID,

                'userID' =>
                    $client->userID,

                'personID' =>
                    $client->personID,

                'user' =>
                    $client->user,

                'person' =>
                    $client->person,
            ]
        ], 200);
    }
}
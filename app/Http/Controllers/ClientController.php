<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with(['user', 'person'])->paginate(10);

        if (request()->wantsJson()) {
            if ($clients->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron clientes.'
                ], 404);
            }

            return response()->json([
                'message' => 'Todos los clientes aquí',
                'data'    => $clients
            ], 200);
        }

        if ($clients->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron clientes.');
        }

        return view('clients.index', compact('clients'));
    }

    public function show($id)
    {
        $client = Client::with(['user', 'person'])->find($id);

        if (request()->wantsJson()) {
            if (!$client) {
                return response()->json([
                    'message' => 'Cliente no encontrado.'
                ], 404);
            }

            return response()->json([
                'message' => 'Datos del cliente',
                'data'    => $client
            ], 200);
        }

        if (!$client) {
            return redirect()->back()->with('error', 'Cliente no encontrado.');
        }

        return view('clients.show', compact('client'));
    }

    public function profile(Request $request)
    {
        // Option A: Si tienes la relación definida en el modelo User ($user->client)
        // $client = $request->user()->client;

        // Option B: Si buscas directamente por la clave foránea (ej. user_id o el ID del usuario)
        $client = Client::where('userID', $request->user()->userID)
        ->first();

        if (!$client) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'No se encontró el perfil de cliente asociado a este usuario.'
                ], 404);
            }

            return redirect()->back()->with('error', 'Perfil no encontrado.');
        }

        // Respuesta Híbrida
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Perfil obtenido correctamente.',
                'data'    => $client->load(['user', 'person'])
            ], 200);
        }

        return view('clients.profile', compact('client'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // user
            'email'        => 'required|string|email|unique:users,email|max:255',
            'password'     => 'required|string|min:8|confirmed|max:255',
            // person
            'name'         => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'phone_number' => 'required|string|max:10',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'email'    => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $person = Person::create([
                'name'         => $request->name,
                'last_name'    => $request->last_name,
                'phone_number' => $request->phone_number,
            ]);

            $client = Client::create([
                'userID'   => $user->userID ?? $user->id,
                'personID' => $person->personID ?? $person->id,
            ]);

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Cliente creado correctamente',
                    'data'    => $client->load(['user', 'person'])
                ], 201);
            }

            return redirect()->back()->with('success', 'Cliente creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al crear el cliente: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Error al crear el cliente: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $client = Client::with(['user', 'person'])->find($id);

        if (!$client) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Cliente no encontrado'
                ], 404);
            }
            return redirect()->back()->with('error', 'Cliente no encontrado.');
        }

        // Obtención de IDs de relaciones para la excepción en validación unique
        $userId = $client->user ? ($client->user->userID ?? $client->user->id) : null;
        $personId = $client->person ? ($client->person->personID ?? $client->person->id) : null;

        $request->validate([
            // user
            'email' => [
                'sometimes', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($userId, 'userID'),
            ],
            'password' => 'sometimes|string|min:8|confirmed|max:255',
            // person
            'name'         => 'sometimes|string|max:255',
            'last_name'    => 'sometimes|string|max:255',
            'rfc'          => [
                'sometimes', 'string', 'max:13',
                Rule::unique('persons', 'rfc')->ignore($personId, 'personID'),
            ],
            'phone_number' => 'sometimes|string|max:10',
        ]);

        try {
            DB::beginTransaction();

            if ($client->user) {
                $userData = $request->only(['email']);
                if ($request->filled('password')) {
                    $userData['password'] = bcrypt($request->password);
                }
                if (!empty($userData)) {
                    $client->user->update($userData);
                }
            }

            if ($client->person) {
                $client->person->update($request->only(['name', 'last_name', 'rfc', 'phone_number']));
            }

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Cliente actualizado correctamente',
                    'data'    => $client->fresh(['user', 'person'])
                ], 200);
            }

            return redirect()->back()->with('success', 'Cliente actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al actualizar el cliente: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Error al actualizar el cliente: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $client = Client::with(['user', 'person'])->find($id);

        if (!$client) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Cliente no encontrado'
                ], 404);
            }
            return redirect()->back()->with('error', 'Cliente no encontrado.');
        }

        try {
            DB::beginTransaction();

            $user = $client->user;
            $person = $client->person;

            // Eliminar el cliente primero
            $client->delete();

            // Eliminar los registros asociados en personas y usuarios
            if ($person) {
                $person->delete();
            }
            if ($user) {
                $user->delete();
            }

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Cliente eliminado correctamente'
                ], 200);
            }

            return redirect()->back()->with('success', 'Cliente eliminado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al eliminar el cliente: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }
}
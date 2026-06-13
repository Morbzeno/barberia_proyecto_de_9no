<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Client;

class ClientController extends Controller
{
    public function index(){
        $Clients = Client::with(['user','person'])->paginate(10);
        if($Clients->isEmpty()){
            return response()->json([
                'message' => 'no se encontraron Clients',
                ],400);
        }

        return response()->json([
            'message' => 'Todos los Clients aquí',
            'data' => $Clients
        ],200);
    }

    public function show($id){
        $Client = Client::with(['user', 'person',])->find($id);

        if (!$Client){
            return response()->json([
                'message' => 'Client no encontrado',
                ],400);
        }

        return response()->json([
            'message' => 'datos del Client',
            'data' => $Client
        ],200);
        
    }

    public function store(Request $request){
        $request->validate([
            // user
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed|max:255',
            // person
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'rfc' => 'required|string|unique:persons,rfc|max:13',
            'phone_number' => 'required|string|max:10',
        ]);

        try {
            return DB::transaction(function () use ($request){
        
                $user = User::create([
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                ]);
                $user->save();

                $person = Person::create([
                    'name' => $request->name,
                    'last_name' => $request->last_name,
                    'rfc' => $request->rfc,
                    'phone_number' => $request->phone_number
                ]);
                $person->save();

                $Client = Client::create([
                    'userID' => $user->userID,
                    'personID' => $person->personID,
                ]);

                return response()->json([
                    'message' => 'Empleado creado correctamente',
                    'data' => $Client
                ], 200);
            });
        } catch (\Exception $e) {
                return response()->json([
                    'message' => $e,
                ], 500);
        }
    }

    public function update(Request $request, $id)
    {
    $Client = Client::with(['user', 'person'])->find($id);

    if (!$Client) {
        return response()->json([
            'message' => 'Empleado no encontrado'
        ], 404);
    }

    $request->validate([
        // user
        'email' => [
            'sometimes', 'string', 'email', 'max:255',
            Rule::unique('users', 'email')->ignore($id, 'userID'),
        ],
        'password' => 'sometimes|string|min:8|confirmed|max:255',
        // person
        'name' => 'sometimes|string|max:255',
        'last_name' => 'sometimes|string|max:255',
        'rfc' => [
            'sometimes', 'string', 'max:13',
            Rule::unique('persons', 'rfc')->ignore($id, 'personID'),
        ],
        'phone_number' => 'sometimes|string|max:10',
    ]);

    try {
        return DB::transaction(function () use ($request, $Client) {
            
            $userData = $request->only(['email']);
            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
            }
            $Client->user->update($userData);

            $Client->person->update($request->only(['name', 'last_name', 'rfc', 'phone_number']));

            return response()->json([
                'message' => 'Empleado actualizado correctamente',
                'data' => $Client->fresh(['user', 'person'])
            ], 200);
        });

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()],500);
        }
    }

    public function destroy($id){
        $Client = Client::find($id);

        if(!$Client){
            return response()->json([
                'message' => 'Client no encontrado'
            ], 404);
        }
        
        $Client->delete();

        return response()->json([
            'message' => 'Client eliminado correctamente'
        ], 200);
    }
}

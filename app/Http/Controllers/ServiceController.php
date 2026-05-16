<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index(){
        $service = Service::paginate(10);
        if($service->isEmpty()){
            return response()->json([
                'message' => 'no se encontraron services',
                ],400);
        }

        return response()->json([
            'message' => 'Todos los services aquí',
            'data' => $service
        ],200);
    }
    
    public function show($id){
        $service = Service::find($id);

        if (!$service){
            return response()->json([
                'message' => 'service no encontrado',
                ],400);
        }

        return response()->json([
            'message' => 'datos del service',
            'data' => $service
        ],200);
    }

     public function store(Request $request){
        $request->validate([
            // user
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|',
            'aproxDuration' => 'required|Integer|',
        ]);

        try {
            return DB::transaction(function () use ($request){
                $service = Service::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'aproxDuration' => $request->aproxDuration
                ]);

                return response()->json([
                    'message' => 'service creado correctamente',
                    'data' => $service
                ], 200);
            });
        } catch (\Exception $e) {
                return response()->json([
                    'message' => $e,
                ], 400);
        }
    }

    public function update(Request $request, $id){
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'price' => 'sometimes|',
            'aproxDuration' => 'sometimes|Integer|',
        ]);

        try {
            return DB::transaction(function () use ($request, $service) {

                $service->update($request->only(['name', 'description', 'price', 'aproxDuration']));

                return response()->json([
                    'message' => 'service actualizado correctamente',
                    'data' => $service->fresh(['user', 'person'])
                ], 200);
            });

            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()]);
            }
    }

    public function destroy($id){
        $service = Service::find($id);

        if(!$service){
            return response()->json([
                'message' => 'service no encontrado'
            ], 404);
        }
        
        $service->delete();

        return response()->json([
            'message' => 'service eliminado correctamente'
        ], 200);
    }
}

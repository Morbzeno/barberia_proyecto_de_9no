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

    public function store(Request $request) {
        // 1. Validación corregida (sin barras sueltas y coincidiendo con Postman)
        $request->validate([
            'name' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'price' => 'required|numeric',
            'aproxDuration' => 'required|integer',
        ]);

        try {
            // 2. Ejecutamos la transacción de forma segura
            $service = DB::transaction(function () use ($request) {
                return Service::create([
                    'name' => $request->name,
                    'description' => $request->descripcion, // Mapea tu JSON en español a la columna de la BD
                    'price' => $request->price,
                    'aproxDuration' => $request->aproxDuration
                ]);
            });

            // 3. Retornamos la respuesta FUERA de la transacción una vez que todo salió bien
            return response()->json([
                'message' => 'Servicio creado correctamente',
                'data' => $service
            ], 201); // 201 es el código HTTP correcto para "Creado con éxito"

        } catch (\Exception $e) {
            // 4. Captura limpia del error sin exponer datos sensibles del servidor
            return response()->json([
                'message' => 'Hubo un error al crear el servicio',
                'error' => $e->getMessage() 
            ], 500);
        }
    }

    public function update(Request $request, $id){
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'message' => 'service no encontrado'
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
                return back()->withInput()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()],500);
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

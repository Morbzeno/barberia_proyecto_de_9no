<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chair;
use App\Models\ChairService;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class ChairController extends Controller
{
    public function index(){
        $chairs = Chair::with('services')->paginate(10);
        if (request()->wantsJson()) {
            if($chairs->isEmpty()){
                return response()->json([
                    'message' => 'no se encontraron Chairs',
                ],400);
            }
            return response()->json([
                'message' => 'Todos los Chairs aquí',
                'data' => $chairs
            ],200);
        }
        if($chairs->isEmpty()){
            return redirect()->back()->with('error', 'No se encontraron Chairs');
        }
        return view('chairs.index', compact('chairs'));
    }

    public function show($id){
        $Chair = Chair::with('chair_services.service')->find($id);

        if (!$Chair){
            return response()->json([
                'message' => 'Chair no encontrado',
                ],400);
        }

        return response()->json([
            'message' => 'datos del Chair',
            'data' => $Chair
        ],200);
        
    }

    public function store(Request $request) {
        $request->validate([
            // Cambiado a string por si el nombre tiene letras. Si es puramente un número, déjalo en integer.
            'chairName' => 'required|string|unique:chairs,chairName',
            'services' => 'required|array',
            'services.*' => 'exists:services,serviceID'
        ]);

        try {
            // Ejecutamos la transacción
            $chair = DB::transaction(function () use ($request) {
                // 1. Creamos la silla
                $chair = Chair::create([
                    'chairName' => $request->chairName,
                ]);

                // 2. ¡La magia de Laravel! Vincula todos los IDs del array de un solo golpe
                $chair->services()->attach($request->services);

                return $chair;
            }); // Aquí cierra el DB::transaction

            // ¡ASEGÚRATE DE QUE ESTA SEA LA RESPUESTA QUE RETORNA TU FUNCIÓN!
            return response()->json([
                'message' => 'Chair creado correctamente',
                'data' => $chair->load('services')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el Chair',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id){
        $request->validate([
            'chairName' => 'required|integer|unique:chairs,chairName,' . $id . ',chairID',
            'services' => 'required|array',
            'services.*' => 'exists:services,serviceID'
        ]);

        try {
            return DB::transaction(function () use ($request, $id){
                $chair = Chair::find($id);
                if (!$chair) {
                    return response()->json([
                        'message' => 'Chair no encontrado',
                    ], 404);
                }

                $chair->update([
                    'chairName' => $request->chairName,
                ]);

                // Eliminar servicios actuales
                ChairService::where('chairID', $id)->delete();

                // Agregar nuevos servicios
                foreach ($request->services as $serviceID) {
                    ChairService::create([
                        'chairID' => $chair->chairID,
                        'serviceID' => $serviceID
                    ]);
                }

                return response()->json([
                    'message' => 'Chair actualizado correctamente',
                    'data' => $chair->fresh('chair_services.service')
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el Chair',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id){
        try {
            return DB::transaction(function () use ($id){
                $chair = Chair::find($id);
                if (!$chair) {
                    return response()->json([
                        'message' => 'Chair no encontrado',
                    ], 404);
                }

                // Eliminar servicios relacionados
                ChairService::where('chairID', $id)->delete();

                // Eliminar la silla
                $chair->delete();

                return response()->json([
                    'message' => 'Chair eliminado correctamente',
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el Chair',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

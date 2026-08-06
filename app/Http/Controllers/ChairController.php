<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chair;
use Illuminate\Support\Facades\DB;

class ChairController extends Controller
{
    public function index()
    {
        $chairs = Chair::with('services')->paginate(10);

        if (request()->wantsJson()) {
            if ($chairs->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron Chairs.'
                ], 404);
            }

            return response()->json([
                'message' => 'Todos los Chairs aquí',
                'data'    => $chairs
            ], 200);
        }

        if ($chairs->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron Chairs.');
        }

        return view('chairs.index', compact('chairs'));
    }

    public function show($id)
    {
        $chair = Chair::with('services')->find($id);

        if (request()->wantsJson()) {
            if (!$chair) {
                return response()->json([
                    'message' => 'Chair no encontrado.'
                ], 404);
            }

            return response()->json([
                'message' => 'Datos del Chair obtenidos correctamente',
                'data'    => $chair
            ], 200);
        }

        if (!$chair) {
            return redirect()->back()->with('error', 'Chair no encontrado.');
        }

        return view('chairs.show', compact('chair'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'chairName'  => 'required|string|unique:chairs,chairName',
            'services'   => 'required|array',
            'services.*' => 'exists:services,serviceID'
        ]);

        try {
            DB::beginTransaction();

            $chair = Chair::create([
                'chairName' => $request->chairName,
            ]);

            // Asocia los servicios mediante la relación pivot
            $chair->services()->attach($request->services);

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Chair creado correctamente',
                    'data'    => $chair->load('services')
                ], 201);
            }

            return redirect()->back()->with('success', 'Chair creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al crear el Chair',
                    'error'   => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al crear el Chair: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $chair = Chair::find($id);

        if (!$chair) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Chair no encontrado'
                ], 404);
            }
            return redirect()->back()->with('error', 'Chair no encontrado');
        }

        $request->validate([
            'chairName'  => 'required|string|unique:chairs,chairName,' . $chair->getKey() . ',chairID',
            'services'   => 'required|array',
            'services.*' => 'exists:services,serviceID'
        ]);

        try {
            DB::beginTransaction();

            $chair->update([
                'chairName' => $request->chairName,
            ]);

            // Actualiza y sincroniza automáticamente las relaciones de servicios
            $chair->services()->sync($request->services);

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Chair actualizado correctamente',
                    'data'    => $chair->load('services')
                ], 200);
            }

            return redirect()->back()->with('success', 'Chair actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al actualizar el Chair',
                    'error'   => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al actualizar el Chair: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $chair = Chair::find($id);

        if (!$chair) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Chair no encontrado'
                ], 404);
            }
            return redirect()->back()->with('error', 'Chair no encontrado');
        }

        try {
            DB::beginTransaction();

            // Desvincula los servicios asociados antes de eliminar la silla
            $chair->services()->detach();

            $chair->delete();

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Chair eliminado correctamente'
                ], 200);
            }

            return redirect()->back()->with('success', 'Chair eliminado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al eliminar el Chair',
                    'error'   => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al eliminar el Chair: ' . $e->getMessage());
        }
    }
}
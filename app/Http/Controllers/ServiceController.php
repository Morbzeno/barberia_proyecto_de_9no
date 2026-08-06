<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::paginate(10);

        if (request()->wantsJson()) {
            if ($services->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron servicios.'
                ], 404);
            }

            return response()->json([
                'message' => 'Servicios obtenidos exitosamente.',
                'data'    => $services
            ], 200);
        }

        if ($services->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron servicios.');
        }

        return view('services.index', compact('services'));
    }

    public function show($id)
    {
        $service = Service::find($id);

        if (request()->wantsJson()) {
            if (!$service) {
                return response()->json([
                    'message' => 'Servicio no encontrado.'
                ], 404);
            }

            return response()->json([
                'message' => 'Servicio obtenido exitosamente.',
                'data'    => $service
            ], 200);
        }

        if (!$service) {
            return redirect()->back()->with('error', 'Servicio no encontrado.');
        }

        return view('services.show', compact('service'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string|max:255',
            'descripcion'   => 'nullable|string|max:255',
            'price'         => 'required|numeric|min:0',
            'aproxDuration' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $description = $request->description ?? $request->descripcion;

            $service = Service::create([
                'name'          => $request->name,
                'description'   => $description,
                'price'         => $request->price,
                'aproxDuration' => $request->aproxDuration,
            ]);

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Servicio creado correctamente.',
                    'data'    => $service
                ], 201);
            }

            return redirect()->back()->with('success', 'Servicio creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al crear el servicio: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Error al crear el servicio: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Servicio no encontrado.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Servicio no encontrado.');
        }

        $request->validate([
            'name'          => 'sometimes|string|max:255',
            'description'   => 'sometimes|string|max:255',
            'descripcion'   => 'sometimes|string|max:255',
            'price'         => 'sometimes|numeric|min:0',
            'aproxDuration' => 'sometimes|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $updateData = $request->only(['name', 'price', 'aproxDuration']);

            if ($request->has('description')) {
                $updateData['description'] = $request->description;
            } elseif ($request->has('descripcion')) {
                $updateData['description'] = $request->descripcion;
            }

            $service->update($updateData);

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Servicio actualizado correctamente.',
                    'data'    => $service->fresh()
                ], 200);
            }

            return redirect()->back()->with('success', 'Servicio actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al actualizar el servicio: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Error al actualizar el servicio: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Servicio no encontrado.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Servicio no encontrado.');
        }

        try {
            DB::beginTransaction();

            $service->delete();

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Servicio eliminado correctamente.'
                ], 200);
            }

            return redirect()->back()->with('success', 'Servicio eliminado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al eliminar el servicio: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al eliminar el servicio: ' . $e->getMessage());
        }
    }
}
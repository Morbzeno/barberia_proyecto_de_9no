<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->q.'%'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Service::create($data);

        return redirect()->route('admin.services.index')->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validated($request);

        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Service $service)
    {
        if ($service->chair_services()->exists()) {
            return back()->with('error', 'No puedes eliminar un servicio asignado a una o más sillas.');
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Servicio eliminado correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'aproxDuration' => 'required|integer|min:1',
        ]);
    }
}

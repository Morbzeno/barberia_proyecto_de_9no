<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chair;
use App\Models\Service;
use Illuminate\Http\Request;

class ChairController extends Controller
{
    public function index()
    {
        $chairs = Chair::with('services')->orderBy('chairName')->paginate(10);

        return view('admin.chairs.index', compact('chairs'));
    }

    public function create()
    {
        $services = Service::orderBy('name')->get();

        return view('admin.chairs.create', compact('services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'chairName' => 'required|string|max:255|unique:chairs,chairName',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,serviceID',
        ]);

        $chair = Chair::create(['chairName' => $data['chairName']]);
        $chair->services()->sync($data['services'] ?? []);

        return redirect()->route('admin.chairs.index')->with('success', 'Silla creada correctamente.');
    }

    public function edit(Chair $chair)
    {
        $services = Service::orderBy('name')->get();
        $chair->load('services');

        return view('admin.chairs.edit', compact('chair', 'services'));
    }

    public function update(Request $request, Chair $chair)
    {
        $data = $request->validate([
            'chairName' => 'required|string|max:255|unique:chairs,chairName,'.$chair->chairID.',chairID',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,serviceID',
        ]);

        $chair->update(['chairName' => $data['chairName']]);
        $chair->services()->sync($data['services'] ?? []);

        return redirect()->route('admin.chairs.index')->with('success', 'Silla actualizada correctamente.');
    }

    public function destroy(Chair $chair)
    {
        if ($chair->appointments()->exists()) {
            return back()->with('error', 'No puedes eliminar una silla con citas asociadas.');
        }

        $chair->services()->detach();
        $chair->delete();

        return redirect()->route('admin.chairs.index')->with('success', 'Silla eliminada correctamente.');
    }
}

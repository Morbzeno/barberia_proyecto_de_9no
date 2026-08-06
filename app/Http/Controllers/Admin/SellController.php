<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sell;
use Illuminate\Http\Request;

class SellController extends Controller
{
    public function index(Request $request)
    {
        $sells = Sell::with('client.person')
            ->when($request->filled('date'), fn ($q) => $q->whereDate('created_at', $request->date))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.sells.index', compact('sells'));
    }

    public function show(Sell $sell)
    {
        $sell->load(['client.person', 'direction', 'cart.producto_cart.producto']);

        return view('admin.sells.show', compact('sell'));
    }

    public function destroy(Sell $sell)
    {
        $sell->delete();

        return redirect()->route('admin.sells.index')->with('success', 'Venta eliminada correctamente.');
    }
}

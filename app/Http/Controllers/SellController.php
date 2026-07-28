<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sell;
use App\Models\ProductsCart;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class SellController extends Controller
{
    public function index()
    {
        $sells = Sell::with(['client', 'direction', 'cart.productsCart.product'])->paginate(10);

        if (request()->wantsJson()) {
            if ($sells->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron ventas.'
                ], 404);
            }

            return response()->json([
                'message' => 'Ventas obtenidas exitosamente.',
                'data'    => $sells
            ], 200);
        }

        if ($sells->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron ventas.');
        }

        return view('sells.index', compact('sells'));
    }

    public function show($id)
    {
        $sell = Sell::with(['client', 'direction', 'cart.productsCart.product'])->find($id);

        if (request()->wantsJson()) {
            if (!$sell) {
                return response()->json([
                    'message' => 'Venta no encontrada.'
                ], 404);
            }

            return response()->json([
                'message' => 'Venta obtenida exitosamente.',
                'data'    => $sell
            ], 200);
        }

        if (!$sell) {
            return redirect()->back()->with('error', 'Venta no encontrada.');
        }

        return view('sells.show', compact('sell'));
    }

    public function store(Request $request, $clientId)
    {
        $request->validate([
            'direction_id'    => 'required|exists:directions,id',
            'purchase_method' => 'nullable|string|max:255',
        ]);

        // Obtener el carrito activo del cliente
        $cart = Cart::where('client_id', $clientId)->first();

        if (!$cart) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'No se encontró un carrito activo para este cliente.'
                ], 404);
            }
            return redirect()->back()->with('error', 'No se encontró un carrito activo para este cliente.');
        }

        $cartId = $cart->id ?? $cart->cart_id;

        // Validar subtotal de productos pendientes
        $total = ProductsCart::where('cart_id', $cartId)
            ->where('state', 'waiting')
            ->sum('subtotal');

        if ($total <= 0) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'El carrito está vacío o no contiene productos pendientes de venta.'
                ], 400);
            }
            return redirect()->back()->with('error', 'El carrito no contiene productos pendientes.');
        }

        try {
            DB::beginTransaction();

            $iva = $total * 0.16;
            $totalConIva = $total + $iva;

            // Crear el registro de la venta
            $sell = Sell::create([
                'cart_id'         => $cartId,
                'client_id'       => $clientId,
                'direction_id'    => $request->direction_id,
                'total'           => $totalConIva,
                'iva'             => $iva,
                'purchase_method' => $request->purchase_method,
            ]);

            $sellId = $sell->id ?? $sell->sell_id;

            // Actualizar el estado de los ítems a "sell" en una sola consulta
            ProductsCart::where('cart_id', $cartId)
                ->where('state', 'waiting')
                ->update([
                    'state'   => 'sell',
                    'sell_id' => $sellId
                ]);

            // Actualizar total del carrito
            $cart->update(['total' => $total]);

            DB::commit();

            $sell->load(['client', 'direction', 'cart']);

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Venta realizada exitosamente.',
                    'data'    => $sell
                ], 201);
            }

            return redirect()->back()->with('success', 'Venta realizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al procesar la venta: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $sell = Sell::find($id);

        if (!$sell) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Venta no encontrada.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Venta no encontrada.');
        }

        try {
            DB::beginTransaction();

            $sellId = $sell->id ?? $sell->sell_id;

            // Revertir estado de los productos en el carrito si es necesario
            ProductsCart::where('sell_id', $sellId)
                ->update([
                    'state'   => 'waiting',
                    'sell_id' => null
                ]);

            $sell->delete();

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Venta eliminada correctamente.'
                ], 200);
            }

            return redirect()->back()->with('success', 'Venta eliminada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al eliminar la venta: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al eliminar la venta: ' . $e->getMessage());
        }
    }
}
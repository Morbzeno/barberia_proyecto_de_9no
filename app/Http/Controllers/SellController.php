<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sell;
use App\Models\ProductsCart;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelPdf\Facades\Pdf;
use Carbon\Carbon;

class SellController extends Controller
{
    public function index()
    {
        $sells = Sell::with(['client', 'direction', 'cart.productsCart.producto'])->paginate(10);

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

    public function store(Request $request, $clientID)
    {
        $request->validate([
            'directionID'    => 'required|exists:directions,directionID',
            'purchase_method' => 'nullable|string|max:255',
        ]);

        // Obtener el carrito activo del cliente
        $cart = Cart::where('clientID', $clientID)->first();

        if (!$cart) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'No se encontró un carrito activo para este cliente.'
                ], 404);
            }
            return redirect()->back()->with('error', 'No se encontró un carrito activo para este cliente.');
        }

        $cartID = $cart->cartID;

        // Validar subtotal de productos pendientes
        $total = ProductsCart::where('cartID', $cartID)
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
                'cartID'         => $cartID,
                'clientID'       => $clientID,
                'directionID'    => $request->directionID,
                'total'           => $totalConIva,
                'iva'             => $iva,
                'purchase_method' => $request->purchase_method,
            ]);

            $sellID = $sell->sellID;

            // Actualizar el estado de los ítems a "sell" en una sola consulta
            ProductsCart::where('cartID', $cartID)
                ->where('state', 'waiting')
                ->update([
                    'state'   => 'sell',
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

    public function destroy($id){
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

            $sellId = $sell->id ?? $sell->sellID;

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

    public function dashboard_pdf($filter, $date){

        if ($filter === 'day') {
            $sells = Sell::with(['client', 'direction', 'cart.productsCart.producto'])->whereDate('created_at', $date)
            ->get();
        }
        else if ($filter === 'month') {
            $sells = Sell::whereMonth('created_at', Carbon::parse($date)->month)
            ->whereYear('created_at', Carbon::parse($date)->year)
            ->with(['client', 'direction', 'cart.productsCart.producto'])
            ->get();
        }
        else if ($filter === 'year') {
            $sells = Sell::whereYear('created_at', Carbon::parse($date)->year)
            ->with(['client', 'direction', 'cart.productsCart.producto'])
            ->get();
        }

        Pdf::view('pdf.sells', ['sales' => $sells])->save('C:/Users/USER/OneDrive/Documents/sells'. $filter . $date .'.pdf');
    }
    public function procesarVentaInterna($clientID, $method = 'Efectivo', $directionID = null)
    {
        $cart = Cart::where('clientID', $clientID)->firstOrFail();
        $total = ProductsCart::where('cartID', $cart->cartID)
            ->where('state', 'waiting')
            ->sum('subtotal');

        if ($total <= 0) {
            throw new \Exception('El carrito no contiene productos válidos.');
        }

        return DB::transaction(function () use ($cart, $clientID, $total, $method, $directionID) {
            $iva = $total * 0.16;
            
            $sell = Sell::create([
                'cartID'          => $cart->cartID,
                'clientID'        => $clientID,
                'directionID'     => $directionID,
                'total'           => $total + $iva,
                'iva'             => $iva,
                'purchase_method' => $method,
            ]);

            ProductsCart::where('cartID', $cart->cartID)
                ->where('state', 'waiting')
                ->update(['state' => 'sell']);

            $cart->update(['total' => $total]);

            return $sell;
        });
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sell;
use App\Models\ProductsCart;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelPdf\Facades\Pdf;
use Carbon\Carbon;

class SellController extends Controller
{
    /**
     * Listar ventas.
     */
    public function index()
    {
        $sells = Sell::with([
            'client',
            'direction',
            'productsCart.producto'
        ])->paginate(10);

        if (request()->wantsJson()) {
            return response()->json([
                'message' => $sells->isEmpty()
                    ? 'No se encontraron ventas.'
                    : 'Ventas obtenidas exitosamente.',
                'data' => $sells
            ], 200);
        }

        if ($sells->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'No se encontraron ventas.');
        }

        return view('sells.index', compact('sells'));
    }

    /**
     * Mostrar una venta.
     */
    public function show($id)
    {
        $sell = Sell::with([
            'client',
            'direction',
            'productsCart.producto'
        ])->find($id);

        if (!$sell) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Venta no encontrada.'
                ], 404);
            }

            return redirect()
                ->back()
                ->with('error', 'Venta no encontrada.');
        }

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Venta obtenida exitosamente.',
                'data' => $sell
            ], 200);
        }

        return view('sells.show', compact('sell'));
    }

    /**
     * Crear una venta.
     */
    public function store(Request $request, $clientID)
    {
        $request->validate([
            'directionID' => 'required|exists:directions,directionID',
            'purchase_method' => 'nullable|string|max:255',
        ]);

        $cart = Cart::where('clientID', $clientID)->first();

        if (!$cart) {
            return $this->errorResponse(
                'No se encontró un carrito activo para este cliente.',
                404
            );
        }

        $items = ProductsCart::where('cartID', $cart->cartID)
            ->where('state', 'waiting')
            ->get();

        if ($items->isEmpty()) {
            return $this->errorResponse(
                'El carrito está vacío o no contiene productos pendientes de venta.',
                400
            );
        }

        try {
            DB::beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | Validar stock y calcular total
            |--------------------------------------------------------------------------
            */

            $total = 0;

            foreach ($items as $item) {
                $product = Product::where(
                    'productID',
                    $item->productID
                )
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw new \Exception(
                        "El producto {$item->productID} ya no existe."
                    );
                }

                if ($product->stock < $item->quantity) {
                    throw new \Exception(
                        "Stock insuficiente para {$product->name}. " .
                        "Disponible: {$product->stock}, " .
                        "solicitado: {$item->quantity}."
                    );
                }

                $total += (float) $item->subtotal;
            }

            if ($total <= 0) {
                throw new \Exception(
                    'El total de la venta no es válido.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Calcular IVA
            |--------------------------------------------------------------------------
            */

            $iva = round($total * 0.16, 2);
            $totalConIva = round($total + $iva, 2);

            /*
            |--------------------------------------------------------------------------
            | Crear venta
            |--------------------------------------------------------------------------
            */

            $sell = Sell::create([
                'cartID' => $cart->cartID,
                'clientID' => $clientID,
                'directionID' => $request->directionID,
                'total' => $totalConIva,
                'iva' => $iva,
                'purchase_method' =>
                    $request->purchase_method ?? 'cash',

                // Descomenta cuando ya exista la columna status:
                // 'status' => 'paid',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Relacionar productos + descontar stock
            |--------------------------------------------------------------------------
            */

            foreach ($items as $item) {
                $product = Product::where(
                    'productID',
                    $item->productID
                )
                    ->lockForUpdate()
                    ->firstOrFail();

                $product->decrement(
                    'stock',
                    $item->quantity
                );

                $item->update([
                    'state' => 'sell',
                    'sellID' => $sell->sellID,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Vaciar total del carrito
            |--------------------------------------------------------------------------
            */

            $cart->update([
                'total' => 0
            ]);

            DB::commit();

            $sell->load([
                'client',
                'direction',
                'productsCart.producto'
            ]);

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Venta realizada exitosamente.',
                    'data' => $sell
                ], 201);
            }

            return redirect()
                ->back()
                ->with(
                    'success',
                    'Venta realizada exitosamente.'
                );

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                'Error al procesar la venta: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Eliminar / revertir una venta.
     */
    public function destroy($id)
    {
        $sell = Sell::with(
            'productsCart.producto'
        )->find($id);

        if (!$sell) {
            return $this->errorResponse(
                'Venta no encontrada.',
                404
            );
        }

        try {
            DB::beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | Restaurar stock
            |--------------------------------------------------------------------------
            */

            foreach ($sell->productsCart as $item) {
                $product = Product::where(
                    'productID',
                    $item->productID
                )
                    ->lockForUpdate()
                    ->first();

                if ($product) {
                    $product->increment(
                        'stock',
                        $item->quantity
                    );
                }

                $item->update([
                    'state' => 'waiting',
                    'sellID' => null
                ]);
            }

            $sell->delete();

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'message' =>
                        'Venta eliminada y stock restaurado correctamente.'
                ], 200);
            }

            return redirect()
                ->back()
                ->with(
                    'success',
                    'Venta eliminada correctamente.'
                );

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                'Error al eliminar la venta: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * PDF de ventas.
     */
    public function dashboard_pdf($filter, $date)
    {
        $query = Sell::with([
            'client',
            'direction',
            'productsCart.producto'
        ]);

        if ($filter === 'day') {
            $query->whereDate(
                'created_at',
                $date
            );

        } elseif ($filter === 'month') {
            $carbonDate = Carbon::parse($date);

            $query
                ->whereMonth(
                    'created_at',
                    $carbonDate->month
                )
                ->whereYear(
                    'created_at',
                    $carbonDate->year
                );

        } elseif ($filter === 'year') {
            $query->whereYear(
                'created_at',
                Carbon::parse($date)->year
            );
        }

        $sells = $query->get();

        Pdf::view(
            'pdf.sells',
            ['sales' => $sells]
        )->save(
            'C:/Users/USER/OneDrive/Documents/sells' .
            $filter .
            $date .
            '.pdf'
        );
    }

    /**
     * Respuesta de error común.
     */
    private function errorResponse(
        string $message,
        int $status
    ) {
        if (request()->wantsJson()) {
            return response()->json([
                'message' => $message
            ], $status);
        }

        return redirect()
            ->back()
            ->with('error', $message);
    }
}
<?php
namespace App\Http\Controllers;
use App\Models\Sell;
use App\Models\Product;
use App\Models\ProductsCart;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellController extends Controller
{
    // Crear una venta
    public function store(Request $request)
    {

        // Obtener usuario autenticado
        $user = Auth::guard('web')->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Debes iniciar sesión.'
            ], 401);
        }

        // Obtener cliente asociado
        $client = $user->client;

        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'El usuario no tiene un perfil de cliente.'
            ], 403);
        }

        $clientID = $client->clientID;

        // Validar datos
       $request->validate([
    'delivery_method' => 'required|in:pickup,delivery',

    'directionID' => [
        'nullable',
        'integer',
        'exists:directions,directionID',
        'required_if:delivery_method,delivery'
    ],

    'purchase_method' => 'nullable|string|max:255',
]);

if ($request->delivery_method === 'delivery') {

    $directionExists = DB::table('directions')
        ->where('directionID', $request->directionID)
        ->where('userID', $user->userID)
        ->exists();

    if (!$directionExists) {
        return response()->json([
            'status' => 'error',
            'message' => 'La dirección seleccionada no pertenece al usuario.'
        ], 403);
    }
}
        try {

            DB::beginTransaction();

            // Obtener carrito del cliente
            $cart = Cart::where('clientID', $clientID)
    ->where('status', 'ACTIVE')
    ->lockForUpdate()
    ->first();

            if (!$cart) {

                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontró un carrito para este cliente.'
                ], 404);
            }

            // Obtener productos pendientes del carrito
            $cartItems = ProductsCart::where('cartID', $cart->cartID)
                ->where('state', 'waiting')
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {

                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'El carrito está vacío.'
                ], 400);
            }

            $total = 0;

            // Validar nuevamente el stock
            foreach ($cartItems as $cartItem) {

                $product = Product::where(
                    'productID',
                    $cartItem->productID
                )
                ->lockForUpdate()
                ->first();

                if (!$product) {

                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Uno de los productos ya no existe.'
                    ], 404);
                }

                if ($product->state !== 'ACTIVO') {

                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'El producto "' . $product->name . '" ya no está disponible.'
                    ], 409);
                }

                if ($product->stock < $cartItem->quantity) {

                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'No hay suficiente stock de "' . $product->name . '".',
                        'stock' => $product->stock,
                        'quantity' => $cartItem->quantity
                    ], 409);
                }

                // Volver a calcular subtotal usando precio actual de BD
                $cartItem->subtotal =
                    $cartItem->quantity * $product->sell_price;

                $cartItem->save();

                $total += $cartItem->subtotal;
            }

            // Calcular IVA
            $iva = $total * 0.16;

            $totalConIva =
                $total + $iva;

            // Crear venta
           $sell = Sell::create([
    'cartID' => $cart->cartID,
    'clientID' => $clientID,

    'directionID' => $request->delivery_method === 'delivery'
        ? $request->directionID
        : null,

    'total' => $totalConIva,
    'iva' => $iva,
    'purchase_method' => $request->purchase_method,
    'delivery_method' => $request->delivery_method,
]);

            // Descontar stock y marcar productos como vendidos
            foreach ($cartItems as $cartItem) {

                $product = Product::where(
                    'productID',
                    $cartItem->productID
                )
                ->lockForUpdate()
                ->first();

                $product->stock -=
                    $cartItem->quantity;

                $product->save();

                $cartItem->state = 'sell';
                $cartItem->save();
            }

            // Vaciar total del carrito activo
            // Marcar todos los productos como vendidos
$cart->total = 0;
$cart->status = 'COMPLETED';
$cart->save();

// Crear un carrito nuevo para futuras compras
Cart::create([
    'clientID' => $clientID,
    'total' => 0,
    'status' => 'ACTIVE',
]);

DB::commit();

            // Cargar relaciones de la venta
            $sell->load([
                'cart',
                'client',
                'direction'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Venta realizada correctamente.',
                'data' => $sell
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Hubo un problema al realizar la venta: ' . $e->getMessage()
            ], 500);
        }
    }


    // Mostrar todas las ventas
    public function index()
    {
        $sells = Sell::with([
            'cart',
            'client',
            'direction'
        ])
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $sells
        ], 200);
    }


    // Mostrar una venta específica
    public function show($id)
    {
        $sell = Sell::with([
            'cart',
            'client',
            'direction'
        ])
        ->find($id);

        if (!$sell) {
            return response()->json([
                'status' => 'error',
                'message' => 'Venta no encontrada.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $sell
        ], 200);
    }


    // Eliminar una venta
    public function destroy($id)
    {
        $sell = Sell::find($id);

        if (!$sell) {
            return response()->json([
                'status' => 'error',
                'message' => 'Venta no encontrada.'
            ], 404);
        }

        $sell->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Venta eliminada correctamente.'
        ], 200);
    }
   
// =====================================================
// MIS COMPRAS
// =====================================================

public function myPurchases()
{
    $user = Auth::guard('web')->user();

    if (!$user) {

        return redirect()
            ->route('login');
    }

    $client = $user->client;

    if (!$client) {

        return redirect()
            ->route('home')
            ->with(
                'error',
                'No existe un perfil de cliente.'
            );
    }

    $sells = Sell::with([
        'direction',
        'payment',
        'cart.producto_cart' => function ($query) {
    $query->where('state', 'sell')
          ->with('producto');
}
    ])
    ->where(
        'clientID',
        $client->clientID
    )
    ->orderBy(
        'created_at',
        'desc'
    )
    ->paginate(10);

    return view(
        'client.purchases.index',
        compact('sells')
    );
}
// =====================================================
// DETALLE DE COMPRA
// =====================================================

public function showPurchase($id)
{
    $user = Auth::guard('web')->user();

    if (!$user) {
        return redirect()->route('login');
    }

    $client = $user->client;

    if (!$client) {
        return redirect()
            ->route('home')
            ->with(
                'error',
                'No existe un perfil de cliente.'
            );
    }

    $sell = Sell::with([
        'direction',
        'payment',
        'cart.producto_cart' => function ($query) {
    $query->where('state', 'sell')
          ->with('producto');
}
    ])
    ->where('clientID', $client->clientID)
    ->where('sellID', $id)
    ->firstOrFail();

    return view(
        'client.purchases.show',
        compact('sell')
    );
}
}
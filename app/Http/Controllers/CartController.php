<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductsCart;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
// implements HasMiddleware
{

  public function index(Request $request)
{
    $user = Auth::guard('web')->user();

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Debes iniciar sesión.'
        ], 401);
    }

    $client = $user->client;

    if (!$client) {
        return response()->json([
            'status' => 'error',
            'message' => 'El usuario no tiene un perfil de cliente.'
        ], 403);
    }

    $clientID = $client->clientID;

    $cart = Cart::with([
        'client',
        'producto_cart' => function ($query) {
            $query->where('state', 'waiting')
                ->with('producto');
        }
    ])
    ->where('clientID', $clientID)
->where('status', 'ACTIVE')
->first();

    if (!$cart) {
        return response()->json([
            'status' => 'success',
            'data' => null,
            'message' => 'El carrito está vacío.'
        ], 200);
    }

    return response()->json([
        'status' => 'success',
        'data' => $cart
    ], 200);
}
    public function show($id)
    {

        $cart = Cart::with(['client', 'producto_cart' => function ($query) {
            $query->where('state', 'waiting')->with('producto');
        }])
->where('clientID', $id)
->where('status', 'ACTIVE')
->get();
        $cart->load(['client', 'producto_cart']);
        
        return response()->json([
            'status' => 'success',
            'data' => $cart
        ], 200);
    }
    

public function add(Request $request)
{
    try {

        // Obtener usuario autenticado
        $user = Auth::guard('web')->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Debes iniciar sesión.'
            ], 401);
        }

        // Obtener cliente asociado al usuario
        $client = $user->client;

        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'El usuario no tiene un perfil de cliente.'
            ], 403);
        }

        $clientID = $client->clientID;

        // Validar los datos
        $request->validate([
            'id' => 'required|integer|exists:products,productID',
            'quantity' => 'required|integer|min:1',
        ]);

        // Obtener producto
        $product = Product::findOrFail($request->id);

        // Verificar que el producto esté activo
        if ($product->state !== 'ACTIVO') {
            return response()->json([
                'status' => 'error',
                'message' => 'Este producto no está disponible actualmente.'
            ], 409);
        }

        // Verificar que exista stock
        if ($product->stock <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Este producto está agotado.'
            ], 409);
        }

        $price = $product->sell_price;

        // Obtener o crear carrito
$cart = Cart::firstOrCreate(
    [
        'clientID' => $clientID,
        'status' => 'ACTIVE',
    ],
    [
        'total' => 0,
    ]
);

        // Buscar producto en el carrito
        $productCart = ProductsCart::where('cartID', $cart->cartID)
            ->where('productID', $product->productID)
            ->where('state', 'waiting')
            ->first();

        // Calcular cantidad que quedaría en el carrito
        $currentQuantity = $productCart
            ? $productCart->quantity
            : 0;

        $newQuantity =
            $currentQuantity + $request->quantity;

        // Verificar stock disponible
        if ($newQuantity > $product->stock) {
            return response()->json([
                'status' => 'error',
                'message' => 'No hay suficiente stock disponible.',
                'stock' => $product->stock,
                'quantity' => $currentQuantity
            ], 409);
        }

        if ($productCart) {

            // Actualizar cantidad
            $productCart->quantity = $newQuantity;

            // Recalcular subtotal
            $productCart->subtotal =
                $newQuantity * $price;

            $productCart->save();

        } else {

            // Crear producto en carrito
            ProductsCart::create([
                'cartID' => $cart->cartID,
                'productID' => $product->productID,
                'quantity' => $request->quantity,
                'subtotal' => $price * $request->quantity,
                'state' => 'waiting',
            ]);
        }

        // Recalcular total
        $total = ProductsCart::where('cartID', $cart->cartID)
            ->where('state', 'waiting')
            ->sum('subtotal');

        $cart->total = $total;
        $cart->save();

        // Cargar relaciones
        $cart->load([
            'client',
            'producto_cart' => function ($query) {
                $query->where('state', 'waiting')
                    ->with('producto');
            }
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $cart
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {

        return response()->json([
            'status' => 'error',
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {

        return response()->json([
            'status' => 'error',
            'message' => 'Hubo un problema al añadir el producto: ' . $e->getMessage()
        ], 500);
    }
}
   public function quitItem(Request $request, $id)
{
    try {
        // Obtener usuario autenticado
        $user = Auth::guard('web')->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Debes iniciar sesión.'
            ], 401);
        }

        // Obtener cliente asociado al usuario
        $client = $user->client;

        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'El usuario no tiene un perfil de cliente.'
            ], 403);
        }

        $clientID = $client->clientID;

        // Obtener carrito del cliente
       $cart = Cart::where('clientID', $clientID)
    ->where('status', 'ACTIVE')
    ->first();

        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Carrito no encontrado.'
            ], 404);
        }

        // Buscar producto dentro del carrito
        $productCart = ProductsCart::where('cartID', $cart->cartID)
            ->where('productID', $id)
            ->where('state', 'waiting')
            ->first();

        if (!$productCart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Producto no encontrado en el carrito.'
            ], 404);
        }

        // Eliminar producto del carrito
        $productCart->delete();

        // Recalcular total del carrito
        $cart->total = ProductsCart::where('cartID', $cart->cartID)
            ->where('state', 'waiting')
            ->sum('subtotal');

        $cart->save();

        // Cargar productos actualizados
        $cart->load([
            'client',
            'producto_cart' => function ($query) {
                $query->where('state', 'waiting')
                    ->with('producto');
            }
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Producto eliminado del carrito.',
            'data' => $cart
        ], 200);

    } catch (\Exception $e) {

        return response()->json([
            'status' => 'error',
            'message' => 'Hubo un problema al eliminar el producto: ' . $e->getMessage()
        ], 500);
    }
}

  public function more(Request $request, $id)
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

    // Obtener carrito
    $cart = Cart::where('clientID', $clientID)
    ->where('status', 'ACTIVE')
    ->first();

    if (!$cart) {
        return response()->json([
            'status' => 'error',
            'message' => 'Carrito no encontrado para este cliente.'
        ], 404);
    }

    // Obtener producto
    $product = Product::find($id);

    if (!$product) {
        return response()->json([
            'status' => 'error',
            'message' => 'Producto no encontrado.'
        ], 404);
    }

    // Verificar que siga activo
    if ($product->state !== 'ACTIVO') {
        return response()->json([
            'status' => 'error',
            'message' => 'Este producto ya no está disponible.'
        ], 409);
    }

    // Buscar producto en carrito
    $productCart = ProductsCart::where('cartID', $cart->cartID)
        ->where('productID', $id)
        ->where('state', 'waiting')
        ->first();

    if (!$productCart) {
        return response()->json([
            'status' => 'error',
            'message' => 'Producto no encontrado en el carrito.'
        ], 404);
    }

    // Verificar stock antes de aumentar
    if ($productCart->quantity >= $product->stock) {
        return response()->json([
            'status' => 'error',
            'message' => 'Has alcanzado el stock máximo disponible.',
            'stock' => $product->stock,
            'quantity' => $productCart->quantity
        ], 409);
    }

    // Aumentar cantidad
    $productCart->quantity += 1;

    // Recalcular subtotal
    $productCart->subtotal =
        $productCart->quantity * $product->sell_price;

    $productCart->save();

    // Recalcular total
    $cart->total = ProductsCart::where('cartID', $cart->cartID)
        ->where('state', 'waiting')
        ->sum('subtotal');

    $cart->save();

    // Cargar carrito actualizado
    $cart->load([
        'client',
        'producto_cart' => function ($query) {
            $query->where('state', 'waiting')
                ->with('producto');
        }
    ]);

    return response()->json([
        'status' => 'success',
        'data' => $cart
    ], 200);
}

public function less(Request $request, $id)
{
    // Obtener usuario autenticado
    $user = Auth::guard('web')->user();

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Debes iniciar sesión.'
        ], 401);
    }

    // Obtener cliente asociado al usuario
    $client = $user->client;

    if (!$client) {
        return response()->json([
            'status' => 'error',
            'message' => 'El usuario no tiene un perfil de cliente.'
        ], 403);
    }

    $clientID = $client->clientID;

    // Obtener el carrito del cliente
    $cart = Cart::where('clientID', $clientID)
    ->where('status', 'ACTIVE')
    ->first();

    if (!$cart) {
        return response()->json([
            'status' => 'error',
            'message' => 'Carrito no encontrado para este cliente.'
        ], 404);
    }

    // Verificar si el producto existe
    $product = Product::find($id);

    if (!$product) {
        return response()->json([
            'status' => 'error',
            'message' => 'Producto no encontrado.'
        ], 404);
    }

    // Buscar el producto en el carrito
    $productCart = ProductsCart::where('cartID', $cart->cartID)
        ->where('productID', $id)
        ->where('state', 'waiting')
        ->first();

    if (!$productCart) {
        return response()->json([
            'status' => 'error',
            'message' => 'Producto no encontrado en el carrito.'
        ], 404);
    }

    // No permitir cantidades menores a 1
    if ($productCart->quantity <= 1) {
        return response()->json([
            'status' => 'error',
            'message' => 'La cantidad mínima es 1.'
        ], 422);
    }

    // Disminuir cantidad
    $productCart->quantity -= 1;

    // Recalcular subtotal usando el precio de la BD
    $productCart->subtotal =
        $productCart->quantity * $product->sell_price;

    $productCart->save();

    // Recalcular total del carrito
    $cart->total = ProductsCart::where('cartID', $cart->cartID)
        ->where('state', 'waiting')
        ->sum('subtotal');

    $cart->save();

    // Cargar productos actualizados
    $cart->load([
        'client',
        'producto_cart' => function ($query) {
            $query->where('state', 'waiting')
                ->with('producto');
        }
    ]);

    return response()->json([
        'status' => 'success',
        'data' => $cart
    ], 200);
}
    public function clear(Request $request)
{
    try {
        // Obtener usuario autenticado
        $user = Auth::guard('web')->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Debes iniciar sesión.'
            ], 401);
        }

        // Obtener cliente asociado al usuario
        $client = $user->client;

        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'El usuario no tiene un perfil de cliente.'
            ], 403);
        }

        $clientID = $client->clientID;

        // Obtener carrito del cliente
        $cart = Cart::where('clientID', $clientID)
    ->where('status', 'ACTIVE')
    ->first();

        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Carrito no encontrado.'
            ], 404);
        }

        // Eliminar todos los productos pendientes
        ProductsCart::where('cartID', $cart->cartID)
            ->where('state', 'waiting')
            ->delete();

        // Reiniciar total del carrito
        $cart->total = 0;
        $cart->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Carrito vaciado correctamente.',
            'data' => $cart
        ], 200);

    } catch (\Exception $e) {

        return response()->json([
            'status' => 'error',
            'message' => 'Hubo un problema al vaciar el carrito: ' . $e->getMessage()
        ], 500);
    }
}

    public function createPaypalOrder(Request $request)
    {
        $clientID = $request->input('clientID');
        $cart = Cart::with([
    'producto_cart' => function ($query) {
        $query->where('state', 'waiting')
              ->with('producto');
    }
])
->where('clientID', $clientID)
->where('status', 'ACTIVE')
->first();

        if (!$cart) {
            return response()->json(['status' => 'error', 'message' => 'Carrito no encontrado'], 404);
        }

        // Calcular el total del carrito (sólo los productos en estado 'waiting')
        $total = ProductsCart::where('cartID', $cart->cartID)->where('state', 'waiting')->sum('subtotal');

        // Si el carrito está vacío
        if ($total <= 0) {
            return response()->json(['status' => 'error', 'message' => 'El carrito está vacío'], 400);
        }

        // Obtener la configuración de PayPal
        $paypal_clientID = Config::get('services.paypal.clientID');
        $paypal_secret = Config::get('services.paypal.secret');
        $paypal_url = "https://api-m.sandbox.paypal.com/v2/checkout/orders";

        // Crear la solicitud de pago a PayPal
        $response = Http::withBasicAuth($paypal_clientID, $paypal_secret)
            ->post($paypal_url, [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'USD', // O la moneda que uses
                            'value' => $total
                        ],
                        'description' => 'Compra de productos en tu carrito'
                    ]
                ],
                'application_context' => [
                    'return_url' => url('/api/paypal/return'),
                    'cancel_url' => url('/api/paypal/cancel')
                ]
            ]);

        // Verificar la respuesta de PayPal
        if ($response->failed()) {
            return response()->json(['status' => 'error', 'message' => 'Error al crear la orden de PayPal'], 500);
        }

        // Devolver el ID de la orden o el enlace a PayPal
        $paypal_order = $response->json();

        return response()->json([
            'status' => 'success',
            'order_id' => $paypal_order['id'],
            'paypal_url' => $paypal_order['links'][1]['href'] // URL para redirigir al usuario a PayPal
        ], 200);
    }

    // Método para manejar la respuesta exitosa de PayPal (cuando el usuario paga)
    public function paypalReturn(Request $request)
    {
        // Aquí manejarías la confirmación de la transacción si el pago fue exitoso
        // Podrías verificar el pago con PayPal usando el 'order_id' o 'token' devuelto.
        return response()->json(['status' => 'success', 'message' => 'Pago realizado con éxito']);
    }

    // Método para manejar la cancelación de PayPal (cuando el usuario cancela el pago)
    public function paypalCancel(Request $request)
    {
        // Aquí manejarías si el usuario cancela el pago
        return response()->json(['status' => 'error', 'message' => 'Pago cancelado por el usuario']);
    }
}
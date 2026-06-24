<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductsCart;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
// implements HasMiddleware
{

    public function index(Request $request)
    {
        // $user = $request->user();
        $clientID = $request->input('clientID');
        if (!$clientID) {
            return response()->json([
                'status' => 'error',
                'message' => 'El campo clientID es obligatorio.'
            ], 400);
        }
        $cart = Cart::with(['client', 'producto_cart' => function ($query) {
            $query->where('state', 'waiting')->with('producto');
        }])->where('clientID', $clientID)->get();
        
        if ($cart->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Carrito no encontrado'
            ], 404);
        }
        
        // Si la solicitud pide JSON, devuelve los datos como JSON.
        if ($request->wantsJson()) {
            return response()->json($cart);
        }
        
        // Pasa los datos del carrito a la vista.
        return response()->json([
            'status' => 'success',
            'data' => $cart
        ], 200);
        

    }
    public function show($id)
    {

        $cart = Cart::with(['client', 'producto_cart' => function ($query) {
            $query->where('state', 'waiting')->with('producto');
        }])->where('clientID', $id)->get();

        $cart->load(['client', 'producto_cart']);
        
        return response()->json([
            'status' => 'success',
            'data' => $cart
        ], 200);
    }
    

public function add(Request $request)
{
    // 1. Mueve la validación fuera del try-catch si quieres que Laravel 
    // maneje automáticamente los errores de validación en formato JSON.
    // O déjala adentro, pero respondiendo JSON en el catch.
    
    $clientID = $request->input('clientID');

    try {
        // Validar los datos del formulario
        $request->validate([
            'clientID' => 'required', // ¡Es buena idea validar también esto!
            'id' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        // Obtener o crear un carrito asociado al cliente
        $cart = Cart::firstOrCreate(
            ['clientID' => $clientID],
            ['total' => 0]
        );

        // Registrar o actualizar el producto en `products_cart`
        $productCart = ProductsCart::where('cartID', $cart->cartID)
            ->where('productID', $request->id)
            ->where('state', 'waiting')
            ->first();

        if ($productCart) {
            // Si ya existe, incrementar la cantidad y el subtotal
            $productCart->quantity += $request->quantity;
            $productCart->subtotal += $request->price * $request->quantity;
            $productCart->save();

        } else {
            // Si no existe, crear un nuevo registro
            ProductsCart::create([
                'cartID' => $cart->cartID,
                'productID' => $request->id,
                'quantity' => $request->quantity,
                'subtotal' => $request->price * $request->quantity,
                'state' => 'waiting',
            ]);
        }

        // Calcular el nuevo total del carrito basado en los productos "waiting"
        $total = ProductsCart::where('cartID', $cart->cartID)
            ->where('state', 'waiting')
            ->sum('subtotal');

        // Actualizar el total del carrito
        $cart->total = $total;
        $cart->save();

        $cart->load(['client', 'producto_cart']);

        return response()->json([
            'status' => 'success',
            'data' => $cart
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // SOLUCIÓN: Responder con JSON en lugar de usar back()
        return response()->json([
            'status' => 'error',
            'message' => 'Error de validación',
            'errors' => $e->errors() // Esto te dirá exactamente QUÉ campo falló
        ], 422);

    } catch (\Exception $e) {
        // SOLUCIÓN: Responder con JSON para errores generales
        return response()->json([
            'status' => 'error',
            'message' => 'Hubo un problema al añadir el producto: ' . $e->getMessage()
        ], 500);
    }
}



    public function quitItem(Request $request, $id)
    {
        $clientID = $request->input('clientID');
        try {
            // Eliminar el ítem del carrito de la biblioteca Cart


            // Eliminar el registro correspondiente en `products_cart`
            ProductsCart::where('cartID', Cart::where('clientID', $clientID)->value('id'))
                        ->where('productID', $id)->where('state', 'waiting')
                        ->delete();
                        $total=ProductsCart::where('cartID', Cart::where('clientID', $clientID)->value('id'))->where('state', 'waiting')
                        ->sum('subtotal');


            // Actualizar el total del carrito
            $cart = Cart::where('clientID', $clientID)->first();
            if ($cart) {
                $cart->total = $total;
                $cart->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Sell deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Hubo un problema al eliminar el producto: ' . $e->getMessage()]);
        }
    }

   public function more(Request $request, $id)
{
    $clientID = $request->input('clientID');

    // 1. Obtener el carrito del cliente
    $cart = Cart::where('clientID', $clientID)->first();
    
    // CORRECCIÓN: Si no encuentra el carrito, $cart será null. 
    // Usamos '!$cart' en lugar de 'isEmpty()'.
    if (!$cart) {
        return response()->json(['status' => 'error', 'message' => 'Carrito no encontrado para este cliente.'], 404);
    }

    // 2. Verificar si el producto existe en la base de datos
    $product = Product::find($id);
    
    // CORRECCIÓN: Si find() no encuentra el registro, devuelve null.
    // Además, tenías '!$product->isEmpty()', lo que significaba "Si NO está vacío (o sea, si existe), da error". Estaba invertido.
    if (!$product) {
        return response()->json(['status' => 'error', 'message' => 'Producto no encontrado.'], 404);
    }

    // 3. Buscar el producto en el carrito con estado 'waiting'
    $productCart = ProductsCart::where('cartID', $cart->id)
        ->where('productID', $id)
        ->where('state', 'waiting')
        ->first();

    if (!$productCart) {
        return response()->json(['status' => 'error', 'message' => 'Producto no encontrado en el carrito con el estado requerido.'], 404);
    }

    // 4. Actualizar la cantidad y el subtotal del producto
    $productCart->quantity += 1;
    $productCart->subtotal = $productCart->quantity * $product->sell_price; // Precio directo de la base de datos
    $productCart->save();

    // 5. Recalcular el total del carrito
    $cart->total = ProductsCart::where('cartID', $cart->id)
        ->where('state', 'waiting')
        ->sum('subtotal');
    $cart->save();

    return response()->json(['status' => 'success', 'data' => $cart], 200);
}





    public function less(Request $request, $id)
    {
        $clientID = $request->input('clientID');
   // Obtener el ID del cliente autenticado
   
        // $clientId = auth()->id();
        // if (!$clientId) {
        //     return response()->json(['status' => 'error', 'message' => 'Usuario no autenticado.'], 401);
        // }
    
        // Obtener el carrito del cliente
        $cart = Cart::where('clientID', $clientID)->first();
        if (!$cart) {
            return response()->json(['status' => 'error', 'message' => 'Carrito no encontrado para este cliente.'], 404);
        }
    
        // Verificar si el producto existe en la base de datos
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Producto no encontrado.'], 404);
        }
    
        // Buscar el producto en el carrito con estado 'waiting'
        $productCart = ProductsCart::where('cartID', $cart->id)
            ->where('productID', $id)
            ->where('state', 'waiting')->where('quantity', '>', 1)
            ->first();
    
        if (!$productCart) {
            return response()->json(['status' => 'error', 'message' => 'Producto no encontrado en el carrito con el estado requerido.'], 404);
        }
    
        // Actualizar la cantidad y el subtotal del producto
        $productCart->quantity -= 1;
        $productCart->subtotal = $productCart->quantity * $product->sell_price; // Precio directo de la base de datos
        $productCart->save();
    
        // Recalcular el total del carrito sumando todos los subtotales de los productos en el carrito
        $cart->total = ProductsCart::where('cartID', $cart->id)->where('state', 'waiting')->sum('subtotal');
        $cart->save();
    
        return response()->json(['status' => 'success', 'data' => $cart], 200);
    }
    
    public function clear(Request $request)
    {
        $clientID = $request->input('clientID');
        try {
            // Eliminar el ítem del carrito de la biblioteca Cart


            // Eliminar el registro correspondiente en `products_cart`
            ProductsCart::where('cartID', Cart::where('clientID', $clientID)->value('id'))->where('state', 'waiting')
                        ->delete();
                        $total=ProductsCart::where('cartID', Cart::where('clientID', $clientID)->value('id'))->where('state', 'waiting')
                        ->sum('subtotal');


            // Actualizar el total del carrito
            $cart = Cart::where('clientID', $clientID)->first();
            if ($cart) {
                $cart->total = 0;
                $cart->save();
            }
if(!$cart){
    return response()->json([
        'status' => 'success',
        'message' => 'cart not found'
    ], 404);
}
            return response()->json([
                'status' => 'success',
                'message' => 'cart deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Hubo un problema al eliminar el producto: ' . $e->getMessage()]);
        }
    }


    public function createPaypalOrder(Request $request)
    {
        $clientID = $request->input('clientID');
        $cart = Cart::with(['producto_cart' => function ($query) {
            $query->where('state', 'waiting')->with('producto');
        }])->where('clientID', $clientID)->first();

        if (!$cart) {
            return response()->json(['status' => 'error', 'message' => 'Carrito no encontrado'], 404);
        }

        // Calcular el total del carrito (sólo los productos en estado 'waiting')
        $total = ProductsCart::where('cartID', $cart->id)->where('state', 'waiting')->sum('subtotal');

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
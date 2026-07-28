<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Client;
use App\Models\ProductsCart;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{

    public function show($id){
        if (!$id) {
            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El campo clientID es obligatorio.'
                ], 400);
            }
            return back()->with('error', 'El campo clientID es obligatorio.');
        }

        $cart = Cart::with(['client', 'producto_cart' => function ($query) {
            $query->where('state', 'waiting')->with('producto');
        }])->where('clientID', $id)->get();

        if (request()->wantsJson()) {
            if ($cart->isEmpty()) {
                return response()->json([
                    "message" => "Carrito no encontrado."
                ], 404);
            }
            return response()->json([
                'status' => 'success',
                'data' => $cart
            ], 200);
        }
        
        return view('cart.show', compact('cart'));
    }
    
    public function add($productID, $clientID){
        try {
            $product = Product::find($productID);

            if (!$product) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'El producto no existe o no está activo.'
                    ], 404);
                }
                return back()->with('error', 'El producto no existe o no está activo.');
            }

            if ($product->stock <= 0) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'El producto no tiene stock disponible.'
                    ], 400);
                }
                return back()->with('error', 'El producto no tiene stock disponible.');
            }

            $client = Client::find($clientID);

            if (!$client) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'El cliente no está registrado.'
                    ], 404);
                }
                return back()->with('error', 'El cliente no está registrado.');
            }

            $cart = Cart::firstOrCreate(
                ['clientID' => $clientID],
                ['total' => 0]
            );

            $productCart = ProductsCart::where('cartID', $cart->cartID)
                ->where('productID', $product->productID)
                ->where('state', 'waiting')
                ->first();

            $precioProducto = (float) $product->sell_price;

            if ($productCart) {
                $productCart->quantity += 1;
                $productCart->subtotal = $productCart->quantity * $precioProducto;
                $productCart->save();
            } else {
                ProductsCart::create([
                    'cartID'    => $cart->cartID,
                    'productID' => $product->productID,
                    'quantity'  => 1,
                    'subtotal'  => $precioProducto,
                    'state'     => 'waiting',
                ]);
            }

            $total = ProductsCart::where('cartID', $cart->cartID)
                ->where('state', 'waiting')
                ->sum('subtotal');

            $cart->total = $total;
            $cart->save();

            $cart->load(['client', 'producto_cart']);

            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Producto añadido con éxito',
                    'data' => $cart
                ], 200);
            }

            return back()->with('success', 'Producto añadido con éxito');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El producto especificado no existe.'
                ], 404);
            }
            return back()->with('error', 'El producto especificado no existe.');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hubo un problema al añadir el producto: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Hubo un problema al añadir el producto: ' . $e->getMessage());
        }
    }

    public function quitItem($productID, $clientID){
        try {
            $product = Product::find($productID);

            if (!$product) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'El producto no existe o no está activo.'
                    ], 404);
                }
                return back()->with('error', 'El producto no existe o no está activo.');
            }

            $client = Client::find($clientID);

            if (!$client) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'El cliente no está registrado.'
                    ], 404);
                }
                return back()->with('error', 'El cliente no está registrado.');
            }
            
            $cart = Cart::where('clientID', $clientID)->first();

            if ($cart) {
                ProductsCart::where('cartID', $cart->cartID)
                    ->where('productID', $productID)
                    ->where('state', 'waiting')
                    ->delete();

                $total = ProductsCart::where('cartID', $cart->cartID)
                    ->where('state', 'waiting')
                    ->sum('subtotal');

                $cart->total = $total;
                $cart->save();
            }

            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Producto eliminado con éxito'
                ], 200);
            } else {
                return back()->with('success', 'Producto eliminado con éxito');
            }

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hubo un problema al eliminar el producto: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Hubo un problema al eliminar el producto: ' . $e->getMessage()]);
        }
    }

    public function more($productID, $clientID){
        $product = Product::find($productID);

        if (!$product) {
            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El producto no existe o no está activo.'
                ], 404);
            }
            return back()->with('error', 'El producto no existe o no está activo.');
        }

        $client = Client::find($clientID);

        if (!$client) {
            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El cliente no está registrado.'
                ], 404);
            }
            return back()->with('error', 'El cliente no está registrado.');
        }

        // 1. Obtener el carrito del cliente
        $cart = Cart::where('clientID', $clientID)->first();

        if (!$cart) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Carrito no encontrado para este cliente.'], 404);
            }
            return back()->with('error', 'Carrito no encontrado para este cliente.');
        }

        // 2. Buscar el producto en el carrito con estado 'waiting'
        $productCart = ProductsCart::where('cartID', $cart->cartID)
            ->where('productID', $productID)
            ->where('state', 'waiting')
            ->first();

        if (!$productCart) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Producto no encontrado en el carrito con el estado requerido.'], 404);
            }
            return back()->with('error', 'Producto no encontrado en el carrito con el estado requerido.');
        }

        $productCart->quantity += 1;
        $productCart->subtotal = $productCart->quantity * $product->sell_price;
        $productCart->save();

        // 3. Recalcular el total del carrito
        $cart->total = ProductsCart::where('cartID', $cart->cartID)
            ->where('state', 'waiting')
            ->sum('subtotal');
        $cart->save();

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'data' => $cart], 200);
        }

        return back()->with('success', 'Cantidad incrementada con éxito');
    }

    public function less($productID, $clientID){
        $product = Product::find($productID);

        if (!$product) {
            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El producto no existe o no está activo.'
                ], 404);
            }
            return back()->with('error', 'El producto no existe o no está activo.');
        }

        $client = Client::find($clientID);

        if (!$client) {
            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El cliente no está registrado.'
                ], 404);
            }
            return back()->with('error', 'El cliente no está registrado.');
        }
 
        $cart = Cart::where('clientID', $clientID)->first();
        if (!$cart) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Carrito no encontrado para este cliente.'], 404);
            }
            return back()->with('error', 'Carrito no encontrado para este cliente.');
        }
    
        // Buscar el producto en el carrito con estado 'waiting' y cantidad mayor a 1
        $productCart = ProductsCart::where('cartID', $cart->cartID)
            ->where('productID', $productID)
            ->where('state', 'waiting')
            ->where('quantity', '>', 1)
            ->first();
    
        if (!$productCart) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Producto no encontrado en el carrito o la cantidad es la mínima (1).'], 404);
            }
            return back()->with('error', 'Producto no encontrado en el carrito o la cantidad es la mínima (1).');
        }
    
        // Actualizar la cantidad y el subtotal del producto
        $productCart->quantity -= 1;
        $productCart->subtotal = $productCart->quantity * $product->sell_price;
        $productCart->save();
    
        // Recalcular el total del carrito sumando todos los subtotales
        $cart->total = ProductsCart::where('cartID', $cart->cartID)->where('state', 'waiting')->sum('subtotal');
        $cart->save();
    
        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'data' => $cart], 200);
        }

        return back()->with('success', 'Cantidad disminuida con éxito');
    }
    
    public function clear(Request $request){
        $clientID = $request->input('clientID');
        try {
            $cart = Cart::where('clientID', $clientID)->first();

            if (!$cart) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Carrito no encontrado'
                    ], 404);
                }
                return back()->with('error', 'Carrito no encontrado');
            }

            // Eliminar los registros correspondientes en `products_cart`
            ProductsCart::where('cartID', $cart->cartID)
                ->where('state', 'waiting')
                ->delete();

            // Actualizar el total del carrito
            $cart->total = 0;
            $cart->save();

            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Carrito vaciado con éxito'
                ], 200);
            }

            return back()->with('success', 'Carrito vaciado con éxito');

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hubo un problema al vaciar el carrito: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Hubo un problema al vaciar el carrito: ' . $e->getMessage()]);
        }
    }

    public function createPaypalOrder(Request $request){
        $clientID = $request->input('clientID');
        $cart = Cart::with(['producto_cart' => function ($query) {
            $query->where('state', 'waiting')->with('producto');
        }])->where('clientID', $clientID)->first();

        if (!$cart) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Carrito no encontrado'], 404);
            }
            return back()->with('error', 'Carrito no encontrado');
        }

        // Calcular el total del carrito (sólo los productos en estado 'waiting')
        $total = ProductsCart::where('cartID', $cart->cartID)->where('state', 'waiting')->sum('subtotal');

        // Si el carrito está vacío
        if ($total <= 0) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'El carrito está vacío'], 400);
            }
            return back()->with('error', 'El carrito está vacío');
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
                            'currency_code' => 'USD',
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
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Error al crear la orden de PayPal'], 500);
            }
            return back()->with('error', 'Error al crear la orden de PayPal');
        }

        $paypal_order = $response->json();

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'order_id' => $paypal_order['id'],
                'paypal_url' => $paypal_order['links'][1]['href']
            ], 200);
        }

        return redirect()->away($paypal_order['links'][1]['href']);
    }

    public function paypalReturn(Request $request){
        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Pago realizado con éxito']);
        }
        return redirect()->route('cart.show', $request->input('clientID'))->with('success', 'Pago realizado con éxito');
    }

    public function paypalCancel(Request $request){
        if (request()->wantsJson()) {
            return response()->json(['status' => 'error', 'message' => 'Pago cancelado por el usuario']);
        }
        return redirect()->route('cart.show', $request->input('clientID'))->with('error', 'Pago cancelado por el usuario');
    }
}
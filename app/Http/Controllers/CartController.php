<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductsCart;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Sell;
use App\Models\Direction;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function show($id){
        $cart = Cart::with(['client', 'products_cart' => function ($query) {
            $query->where('state', 'waiting')->with('producto.images');
        }])->where('clientID', $id)->first();

        if (!$cart) {
            return response()->json(["status" => "error", "message" => "Carrito no encontrado."], 404);
        }

        return response()->json(['status' => 'success', 'data' => [$cart]], 200);
    }
    
    public function add(Request $request, $productID, $clientID){
        try {
            $product = Product::find($productID);
            if (!$product) return response()->json(['status' => 'error', 'message' => 'Producto no encontrado.'], 404);

            $cart = Cart::firstOrCreate(['clientID' => $clientID], ['total' => 0]);

            $productCart = ProductsCart::where('cartID', $cart->cartID)
                ->where('productID', $productID)
                ->where('state', 'waiting')
                ->first();

            if ($productCart) {
                $productCart->quantity += 1;
                $productCart->subtotal = $productCart->quantity * $product->sell_price;
                $productCart->save();
            } else {
                ProductsCart::create([
                    'cartID' => $cart->cartID,
                    'productID' => $productID,
                    'quantity' => 1,
                    'subtotal' => $product->sell_price,
                    'state' => 'waiting'
                ]);
            }
            $this->updateCartTotal($cart);
            return response()->json(['status' => 'success', 'message' => 'Producto añadido'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function more(Request $request, $productID, $clientID){
        return $this->add($request, $productID, $clientID);
    }

    public function less(Request $request, $productID, $clientID){
        try {
            $cart = Cart::where('clientID', $clientID)->first();
            if (!$cart) return response()->json(['status' => 'error', 'message' => 'Carrito no encontrado'], 404);

            $item = ProductsCart::where('cartID', $cart->cartID)
                ->where('productID', $productID)
                ->where('state', 'waiting')
                ->first();

            if ($item && $item->quantity > 1) {
                $item->quantity -= 1;
                $product = Product::find($productID);
                $item->subtotal = $item->quantity * $product->sell_price;
                $item->save();
                $this->updateCartTotal($cart);
                return response()->json(['status' => 'success', 'message' => 'Cantidad disminuida'], 200);
            }
            return response()->json(['status' => 'error', 'message' => 'Mínimo alcanzado'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function quitItem(Request $request, $productID, $clientID){
        try {
            $cart = Cart::where('clientID', $clientID)->first();
            if ($cart) {
                ProductsCart::where('cartID', $cart->cartID)
                    ->where('productID', $productID)
                    ->where('state', 'waiting')
                    ->delete();
                $this->updateCartTotal($cart);
            }
            return response()->json(['status' => 'success', 'message' => 'Producto eliminado'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function updateCartTotal($cart) {
        $cart->total = ProductsCart::where('cartID', $cart->cartID)
            ->where('state', 'waiting')
            ->sum('subtotal');
        $cart->save();
    }

    public function createPaypalOrder(Request $request){
        $clientID = $request->input('clientID');
        $cart = Cart::where('clientID', $clientID)->first();
        if (!$cart || $cart->total <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Carrito vacío'], 400);
        }

        $paypal_clientID = env('PAYPAL_CLIENT_ID');
        $paypal_secret = env('PAYPAL_SECRET');

        if (empty($paypal_clientID) || empty($paypal_secret)) {
            return response()->json(['status' => 'error', 'message' => 'Credenciales de PayPal no encontradas en .env'], 500);
        }

        
       $baseUrl = env(
    'PAYPAL_RETURN_BASE_URL',
    'http://10.0.2.2:8000'
);

        $response = Http::withBasicAuth($paypal_clientID, $paypal_secret)
            ->post("https://api-m.sandbox.paypal.com/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'MXN',
                        'value' => number_format($cart->total, 2, '.', '')
                    ]
                ]],
                'application_context' => [
                    'return_url' => "$baseUrl/api/paypal/return?clientID=$clientID",
                    'cancel_url' => "$baseUrl/api/paypal/cancel"
                ]
            ]);

        if ($response->failed()) {
            Log::error("PAYPAL ERROR: " . $response->body());
            return response()->json([
                'status' => 'error',
                'message' => 'Error de PayPal: ' . ($response->json()['message'] ?? $response->body()),
                'http_code' => $response->status()
            ], 500);
        }

        $data = $response->json();
        $approveUrl = collect($data['links'])->where('rel', 'approve')->first()['href'];

        return response()->json([
            'status' => 'success',
            'paypal_url' => $approveUrl
        ], 200);
    }

    public function paypalReturn(Request $request)
{
    $baseUrl = env(
        'PAYPAL_RETURN_BASE_URL',
        'http://127.0.0.1:8000'
    );

    $clientID = (int) $request->query('clientID');

    $cart = Cart::where('clientID', $clientID)->first();

    if (!$cart) {
        return response(
            'No se encontró el carrito del cliente.',
            404
        );
    }

    $items = ProductsCart::where('cartID', $cart->cartID)
        ->where('state', 'waiting')
        ->get();

    if ($items->isEmpty()) {
        return response(
            'No hay productos pendientes en el carrito.',
            400
        );
    }

    $client = $cart->client;

    if (!$client) {
        return response(
            'No se encontró el cliente.',
            404
        );
    }

    $direction = Direction::where(
        'userID',
        $client->userID
    )->first();

    if (!$direction) {
        return response(
            'El cliente no tiene una dirección registrada.',
            422
        );
    }

    try {
        DB::beginTransaction();

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
                    "Producto {$item->productID} no encontrado."
                );
            }

            if ($product->stock < $item->quantity) {
                throw new \Exception(
                    "Stock insuficiente para {$product->name}."
                );
            }

            $total += (float) $item->subtotal;
        }

        $iva = round($total * 0.16, 2);
        $totalConIva = round($total + $iva, 2);

        $sell = Sell::create([
            'cartID' => $cart->cartID,
            'clientID' => $clientID,
            'directionID' => $direction->directionID,
            'total' => $totalConIva,
            'iva' => $iva,
            'purchase_method' => 'paypal',
            'status' => 'paid',
        ]);

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

        $cart->update([
            'total' => 0
        ]);

        DB::commit();

    } catch (\Exception $e) {
        DB::rollBack();

        return response(
            'Error al registrar la compra: ' . $e->getMessage(),
            500
        );
    }

    return "
    <body style='background-color: #f3f4f6; font-family: system-ui, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;'>
        <div style='background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; max-width: 400px;'>
            <div style='color: #10b981; font-size: 64px; margin-bottom: 20px;'>✓</div>

            <h1 style='color: #1f2937; margin: 0 0 10px 0;'>
                ¡Pago Completado!
            </h1>

            <p style='color: #6b7280; margin-bottom: 30px;'>
                Tu compra fue registrada correctamente.
                Ya puedes regresar a Machin Barber.
            </p>
        </div>
    </body>";
}

    public function paypalCancel(){
        return "
        <body style='background-color: #f3f4f6; font-family: system-ui, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;'>
            <div style='background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; max-width: 400px;'>
                <div style='color: #ef4444; font-size: 64px; margin-bottom: 20px;'>✕</div>
                <h1 style='color: #1f2937; margin: 0 0 10px 0;'>Pago Cancelado</h1>
                <p style='color: #6b7280; margin-bottom: 30px;'>El proceso de pago no se completó. Puedes volver a intentarlo desde tu carrito.</p>
                <a href='#' onclick='window.close();' style='background-color: #374151; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold;'>Cerrar Ventana</a>
            </div>
        </body>";
    }
}

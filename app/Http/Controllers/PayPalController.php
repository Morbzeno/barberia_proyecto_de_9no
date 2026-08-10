<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductsCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Sell;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;

class PayPalController extends Controller
{
    private function baseUrl()
    {
        return env('PAYPAL_MODE') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }


    // =====================================================
    // OBTENER ACCESS TOKEN DE PAYPAL
    // =====================================================

    private function getAccessToken()
    {
        $response = Http::asForm()
            ->withBasicAuth(
                env('PAYPAL_CLIENT_ID'),
                env('PAYPAL_CLIENT_SECRET')
            )
            ->post(
                $this->baseUrl() . '/v1/oauth2/token',
                [
                    'grant_type' => 'client_credentials'
                ]
            );

        if (!$response->successful()) {

            throw new \Exception(
                'No se pudo autenticar con PayPal.'
            );
        }

        return $response->json()['access_token'];
    }


    // =====================================================
    // CREAR ORDEN
    // =====================================================

    public function createOrder(Request $request)
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


        // Validar método de entrega
        $request->validate([
            'delivery_method' =>
                'required|in:pickup,delivery',

            'directionID' => [
                'nullable',
                'integer',
                'exists:directions,directionID',
                'required_if:delivery_method,delivery'
            ]
        ]);


        // Si es envío, comprobar que la dirección sea del usuario
        if ($request->delivery_method === 'delivery') {

            $directionExists =
                \App\Models\Direction::where(
                    'directionID',
                    $request->directionID
                )
                ->where(
                    'userID',
                    $user->userID
                )
                ->exists();


            if (!$directionExists) {

                return response()->json([
                    'status' => 'error',
                    'message' =>
                        'La dirección seleccionada no pertenece al usuario.'
                ], 403);
            }
        }


        // Obtener carrito
        $cart = Cart::where(
    'clientID',
    $client->clientID
)
->where(
    'status',
    'ACTIVE'
)
->first();


        if (!$cart) {

            return response()->json([
                'status' => 'error',
                'message' => 'Carrito no encontrado.'
            ], 404);
        }


        // Obtener productos
        $cartItems =
            ProductsCart::where(
                'cartID',
                $cart->cartID
            )
            ->where(
                'state',
                'waiting'
            )
            ->get();


        if ($cartItems->isEmpty()) {

            return response()->json([
                'status' => 'error',
                'message' => 'El carrito está vacío.'
            ], 400);
        }


        // =====================================================
        // CALCULAR TOTAL DESDE LA BD
        // =====================================================

        $subtotal = 0;


        foreach ($cartItems as $item) {

            $product = Product::find(
                $item->productID
            );


            if (!$product) {

                return response()->json([
                    'status' => 'error',
                    'message' =>
                        'Uno de los productos ya no existe.'
                ], 404);
            }


            if ($product->state !== 'ACTIVO') {

                return response()->json([
                    'status' => 'error',
                    'message' =>
                        'El producto "' .
                        $product->name .
                        '" ya no está disponible.'
                ], 409);
            }


            if ($product->stock < $item->quantity) {

                return response()->json([
                    'status' => 'error',
                    'message' =>
                        'No hay suficiente stock de "' .
                        $product->name .
                        '".'
                ], 409);
            }


            $subtotal +=
                $product->sell_price *
                $item->quantity;
        }


        // IVA 16%
        $iva =
            $subtotal * 0.16;


        $total =
            $subtotal + $iva;


        try {

            // Obtener token
            $accessToken =
                $this->getAccessToken();


            // Crear orden PayPal
            $response =
                Http::withToken(
                    $accessToken
                )
                ->withHeaders([
                    'Content-Type' =>
                        'application/json'
                ])
                ->post(
                    $this->baseUrl() .
                    '/v2/checkout/orders',
                    [
                        'intent' =>
                            'CAPTURE',

                        'purchase_units' => [
                            [
                                'amount' => [
                                    'currency_code' =>
                                        'MXN',

                                    'value' =>
                                        number_format(
                                            $total,
                                            2,
                                            '.',
                                            ''
                                        )
                                ]
                            ]
                        ]
                    ]
                );


            if (!$response->successful()) {

                return response()->json([
                    'status' => 'error',
                    'message' =>
                        'PayPal no pudo crear la orden.',
                    'paypal' =>
                        $response->json()
                ], 500);
            }


            $paypalOrder =
                $response->json();

                // Guardar datos del checkout temporalmente en sesión
session([
    'paypal_checkout_' . $paypalOrder['id'] => [
        'delivery_method' => $request->delivery_method,
        'directionID' => $request->delivery_method === 'delivery'
            ? $request->directionID
            : null,
        'clientID' => $client->clientID,
    ]
]);


            return response()->json([
                'status' => 'success',

                'orderID' =>
                    $paypalOrder['id'],

                'total' =>
                    number_format(
                        $total,
                        2,
                        '.',
                        ''
                    )
            ], 201);


        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' =>
                    'Error conectando con PayPal: ' .
                    $e->getMessage()
            ], 500);
        }
    }


    // =====================================================
    // CAPTURAR ORDEN
    // =====================================================

   public function captureOrder(Request $request)
{
    // =====================================================
    // USUARIO AUTENTICADO
    // =====================================================

    $user = Auth::guard('web')->user();

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Debes iniciar sesión.'
        ], 401);
    }


    // =====================================================
    // CLIENTE
    // =====================================================

    $client = $user->client;

    if (!$client) {
        return response()->json([
            'status' => 'error',
            'message' => 'El usuario no tiene un perfil de cliente.'
        ], 403);
    }


    // =====================================================
    // VALIDAR ORDER ID
    // =====================================================

    $request->validate([
        'orderID' => 'required|string'
    ]);


    try {

        // =====================================================
        // OBTENER TOKEN PAYPAL
        // =====================================================

        $accessToken = $this->getAccessToken();


        // =====================================================
        // CAPTURAR ORDEN PAYPAL
        // =====================================================

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->post(
                $this->baseUrl() .
                '/v2/checkout/orders/' .
                $request->orderID .
                '/capture',
                new \stdClass()
            );


        $paypalData = $response->json();


        // =====================================================
        // VALIDAR CAPTURA
        // =====================================================

        if (
            !$response->successful() ||
            ($paypalData['status'] ?? null) !== 'COMPLETED'
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'El pago no pudo ser confirmado.',
                'paypal' => $paypalData
            ], 400);
        }


        // =====================================================
        // RECUPERAR DATOS DEL CHECKOUT
        // =====================================================

        $checkoutData = session(
            'paypal_checkout_' . $request->orderID
        );


        if (!$checkoutData) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontraron los datos del checkout.'
            ], 400);
        }


        // Verificar que la orden pertenezca al cliente
        if (
            (int) $checkoutData['clientID'] !==
            (int) $client->clientID
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'La orden no pertenece al usuario autenticado.'
            ], 403);
        }


        // =====================================================
        // CONSULTAR ORDEN PAYPAL
        // =====================================================

        $paypalOrderResponse = Http::withToken($accessToken)
            ->acceptJson()
            ->get(
                $this->baseUrl() .
                '/v2/checkout/orders/' .
                $request->orderID
            );


        if (!$paypalOrderResponse->successful()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo verificar la orden de PayPal.'
            ], 400);
        }


        $paypalOrder = $paypalOrderResponse->json();


        // =====================================================
        // INICIAR TRANSACCIÓN
        // =====================================================

        DB::beginTransaction();


        // =====================================================
        // OBTENER CARRITO
        // =====================================================

        $cart = Cart::where(
    'clientID',
    $client->clientID
)
->where(
    'status',
    'ACTIVE'
)
->lockForUpdate()
->first();


        if (!$cart) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Carrito no encontrado.'
            ], 404);
        }


        // =====================================================
        // OBTENER PRODUCTOS WAITING
        // =====================================================

        $cartItems = ProductsCart::where(
            'cartID',
            $cart->cartID
        )
        ->where(
            'state',
            'waiting'
        )
        ->lockForUpdate()
        ->get();


        if ($cartItems->isEmpty()) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'El carrito ya fue procesado.'
            ], 409);
        }


        // =====================================================
        // RECALCULAR SUBTOTAL Y VALIDAR STOCK
        // =====================================================

        $subtotal = 0;

        $productsToUpdate = [];


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
                    'message' =>
                        'El producto "' .
                        $product->name .
                        '" ya no está disponible.'
                ], 409);
            }


            if ($product->stock < $cartItem->quantity) {

                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' =>
                        'Stock insuficiente para "' .
                        $product->name .
                        '".'
                ], 409);
            }


            // Recalcular subtotal desde precio de BD
            $cartItem->subtotal =
                $cartItem->quantity *
                $product->sell_price;

            $cartItem->save();


            $subtotal += $cartItem->subtotal;


            $productsToUpdate[] = [
                'product' => $product,
                'quantity' => $cartItem->quantity
            ];
        }


        // =====================================================
        // CALCULAR IVA Y TOTAL
        // =====================================================

        $iva = round(
            $subtotal * 0.16,
            2
        );


        $total = round(
            $subtotal + $iva,
            2
        );


        // =====================================================
        // VERIFICAR MONTO PAGADO EN PAYPAL
        // =====================================================

        $paypalAmount = (float) (
            $paypalOrder
                ['purchase_units'][0]
                ['amount']['value']
            ?? 0
        );


        $paypalCurrency =
            $paypalOrder
                ['purchase_units'][0]
                ['amount']['currency_code']
            ?? null;


        if (
            $paypalCurrency !== 'MXN' ||
            abs($paypalAmount - $total) > 0.01
        ) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' =>
                    'El monto pagado no coincide con el total del carrito.'
            ], 409);
        }


        // =====================================================
        // CREAR VENTA
        // =====================================================

        $sell = Sell::create([


            'cartID' =>
                $cart->cartID,

            'clientID' =>
                $client->clientID,

            'directionID' =>
                $checkoutData['directionID'],

            'total' =>
                $total,

            'iva' =>
                $iva,

            'purchase_method' =>
                'PAYPAL',

            'delivery_method' =>
                $checkoutData['delivery_method']
        ]);
// =====================================================
// OBTENER CAPTURE ID DE PAYPAL
// =====================================================

$paypalCaptureID =
    $paypalData
        ['purchase_units'][0]
        ['payments']['captures'][0]['id']
    ?? null;


// =====================================================
// REGISTRAR PAGO
// =====================================================

Payment::create([

    'appointmentID' =>
        null,

    'sellID' =>
        $sell->sellID,

    'subtotal' =>
        $total,

    'paymentMethod' =>
        'PAYPAL',

    'paypalOrderID' =>
        $request->orderID,

    'paypalCaptureID' =>
        $paypalCaptureID,

    'currency' =>
        'MXN',

    'status' =>
        $paypalData['status'] ?? 'COMPLETED'
]);

        // =====================================================
        // DESCONTAR STOCK
        // =====================================================

        foreach ($productsToUpdate as $item) {

            $product = $item['product'];

            $product->stock -=
                $item['quantity'];

            $product->save();
        }


        // =====================================================
        // WAITING -> SELL
        // =====================================================

        foreach ($cartItems as $cartItem) {

            $cartItem->state = 'sell';

            $cartItem->save();
        }


        // =====================================================
// CERRAR CARRITO ACTUAL
// =====================================================

$cart->total = 0;
$cart->status = 'COMPLETED';
$cart->save();


// =====================================================
// CREAR NUEVO CARRITO ACTIVO
// =====================================================

Cart::create([
    'clientID' => $client->clientID,
    'total' => 0,
    'status' => 'ACTIVE'
]);

        // =====================================================
        // CONFIRMAR TRANSACCIÓN
        // =====================================================

        DB::commit();


        // =====================================================
        // ELIMINAR DATOS TEMPORALES
        // =====================================================

        session()->forget(
            'paypal_checkout_' .
            $request->orderID
        );


        // =====================================================
        // RESPUESTA
        // =====================================================

        return response()->json([
            'status' => 'success',

            'message' =>
                'Pago y venta procesados correctamente.',

            'data' => [

                'sellID' =>
                    $sell->sellID,

                'total' =>
                    $sell->total,

                'iva' =>
                    $sell->iva,

                'delivery_method' =>
                    $checkoutData['delivery_method'],

                'directionID' =>
                    $checkoutData['directionID']
            ]
        ], 200);


    } catch (\Exception $e) {

        // Si hay una transacción activa, revertirla
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }


        return response()->json([
            'status' => 'error',

            'message' =>
                'El pago fue realizado, pero ocurrió un error al registrar la venta: ' .
                $e->getMessage()
        ], 500);
    }
}
}
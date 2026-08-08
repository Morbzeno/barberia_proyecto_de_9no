<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Http\Controllers\SellController;

class PayPalController extends Controller
{
    public function createOrder(Request $request)
    {
        $request->validate([
            'client_id'        => 'required|integer',
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|integer',
            'items.*.name'     => 'required|string',
            'items.*.price'    => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'total'            => 'required|numeric|min:0',
        ]);

        try {
            $paypal = new PayPalClient;
            $paypal->setApiCredentials(config('paypal'));
            $token = $paypal->getAccessToken();
            $paypal->setAccessToken($token);

            $paypalItems = collect($request->items)->map(function ($item) {
                return [
                    'name'        => $item['name'],
                    'unit_amount' => [
                        'currency_code' => 'USD',
                        'value'         => number_format($item['price'], 2, '.', ''),
                    ],
                    'quantity'    => $item['quantity'],
                ];
            })->toArray();

            $formattedTotal = number_format($request->total, 2, '.', '');

// En PayPalController.php -> createOrder()

            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        // ⬇️ AGREGA ESTA LÍNEA PARA QUE PAYPAL GUARDE EL ID DEL CLIENTE
                        'custom_id' => (string) $request->client_id, 

                        'amount' => [
                            'currency_code' => 'USD',
                            'value'         => $formattedTotal,
                            'breakdown'     => [
                                'item_total' => [
                                    'currency_code' => 'USD',
                                    'value'         => $formattedTotal,
                                ],
                            ],
                        ],
                        'items' => $paypalItems,
                    ],
                ],
                'application_context' => [
                    'return_url' => route('api.paypal.success'),
                    'cancel_url' => route('api.paypal.cancel'),
                ],
            ];

            $response = $paypal->createOrder($orderData);

            if (isset($response['status']) && $response['status'] === 'CREATED') {
                // Obtención segura de la URL de aprobación
                $approvalUrl = collect($response['links'])->firstWhere('rel', 'approve')['href'] ?? null;

                if (request()->wantsJson()) {
                    return response()->json([
                        'message'      => 'Orden de PayPal creada exitosamente.',
                        'approval_url' => $approvalUrl,
                        'data'         => $response,
                    ], 201);
                }

                if ($approvalUrl) {
                    return redirect()->away($approvalUrl);
                }
            }

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'No se pudo crear la orden en PayPal.',
                    'error'   => $response['message'] ?? $response
                ], 500);
            }

            return redirect()->back()->with('error', 'No se pudo crear la orden en PayPal.');

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al procesar la solicitud con PayPal: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al procesar la solicitud con PayPal: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Token de aprobación de PayPal no proporcionado.'
                ], 400);
            }
            return redirect()->route('home')->with('error', 'Token de PayPal no proporcionado.');
        }

        try {
            $paypal = new PayPalClient;
            $paypal->setApiCredentials(config('paypal'));
            $paypal->setAccessToken($paypal->getAccessToken());

            $response = $paypal->capturePaymentOrder($token);

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {

                // Use the authenticated user's id safely if available
                $clientID = $response['purchase_units'][0]['custom_id']
                    ?? Auth::id()
                    ?? Auth::user()?->clientID;
            
            // Llamas al método/servicio que guarda la venta
                $sell = app(SellController::class)->procesarVentaInterna($clientID, 'PayPal');
                if (request()->wantsJson()) {
                    return response()->json([
                        'message' => 'Pago completado correctamente.',
                        'data'    => $response
                    ], 200);
                }

                return redirect()->route('home')->with('success', 'Pago completado correctamente.');
            }

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'No se pudo completar el pago en PayPal.',
                    'error'   => $response
                ], 400);
            }

            return redirect()->route('home')->with('error', 'No se pudo completar el pago en PayPal.');

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'Error al capturar el pago: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('home')->with('error', 'Error al capturar el pago: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'El pago fue cancelado por el usuario.'
            ], 400);
        }

        return redirect()->route('home')->with('error', 'El pago fue cancelado por el usuario.');
    }
}
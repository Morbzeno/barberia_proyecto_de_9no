<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sell;
use Illuminate\Http\Request;

class SellController extends Controller
{
    // =====================================================
    // LISTAR VENTAS
    // =====================================================

    public function index(Request $request)
    {
        $sells = Sell::with('client.person')
            ->when(
                $request->filled('date'),
                fn ($q) => $q->whereDate(
                    'created_at',
                    $request->date
                )
            )
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.sells.index',
            compact('sells')
        );
    }


    // =====================================================
    // MOSTRAR VENTA
    // =====================================================

    public function show(Sell $sell)
    {
        $sell->load([
            'client.person',
            'direction',
            'cart.producto_cart.producto'
        ]);

        return view(
            'admin.sells.show',
            compact('sell')
        );
    }


    // =====================================================
    // ELIMINAR VENTA
    // =====================================================

    public function destroy(Sell $sell)
    {
        $sell->delete();

        return redirect()
            ->route('admin.sells.index')
            ->with(
                'success',
                'Venta eliminada correctamente.'
            );
    }


    // =====================================================
    // LISTAR PEDIDOS PARA EL ADMINISTRADOR
    // =====================================================

    public function orders(Request $request)
    {
        $sells = Sell::with([
            'client.person',
            'direction',
            'payment'
        ])
        ->when(
            $request->filled('status'),
            fn ($q) => $q->where(
                'order_status',
                $request->status
            )
        )
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view(
            'admin.orders.index',
            compact('sells')
        );
    }


    // =====================================================
    // ACTUALIZAR ESTADO DEL PEDIDO
    // =====================================================

    public function updateOrderStatus(
        Request $request,
        $id
    ) {
        // Buscar pedido
        $sell = Sell::find($id);

        if (!$sell) {
            return redirect()
                ->route('admin.orders')
                ->with(
                    'error',
                    'Pedido no encontrado.'
                );
        }


        // =====================================================
        // ESTADOS PERMITIDOS SEGÚN TIPO DE ENTREGA
        // =====================================================

        if ($sell->delivery_method === 'delivery') {

            $allowedStatuses = [
                'PENDIENTE',
                'PREPARANDO',
                'ENVIADO',
                'ENTREGADO'
            ];

        } else {

            $allowedStatuses = [
                'PENDIENTE',
                'PREPARANDO',
                'LISTO PARA RECOGER',
                'ENTREGADO'
            ];
        }


        // =====================================================
        // VALIDAR ESTADO
        // =====================================================

        $request->validate([
            'order_status' => [
                'required',
                'string',

                function (
                    $attribute,
                    $value,
                    $fail
                ) use ($allowedStatuses) {

                    if (
                        !in_array(
                            $value,
                            $allowedStatuses
                        )
                    ) {
                        $fail(
                            'El estado seleccionado no es válido.'
                        );
                    }
                }
            ]
        ]);


        // =====================================================
        // ACTUALIZAR ESTADO
        // =====================================================

        $sell->order_status =
            $request->order_status;


        // =====================================================
        // CONTROL DE GUÍA
        // =====================================================

        if ($sell->delivery_method === 'delivery') {

    // Generar guía al pasar a ENVIADO
    if (
        $request->order_status === 'ENVIADO' &&
        !$sell->tracking_code
    ) {
        $sell->tracking_code =
            'MB-GUIA-' .
            strtoupper(
                substr(uniqid(), -8)
            );
    }

    // Solo eliminar si regresamos a un estado anterior
    if (
        in_array(
            $request->order_status,
            ['PENDIENTE', 'PREPARANDO']
        )
    ) {
        $sell->tracking_code = null;
    }

} else {

    // Generar código cuando esté listo para recoger
    if (
        $request->order_status === 'LISTO PARA RECOGER' &&
        !$sell->tracking_code
    ) {
        $sell->tracking_code =
            'MB-REC-' .
            strtoupper(
                substr(uniqid(), -8)
            );
    }

    // Solo eliminar si regresamos a un estado anterior
    if (
        in_array(
            $request->order_status,
            ['PENDIENTE', 'PREPARANDO']
        )
    ) {
        $sell->tracking_code = null;
    }
}

        // =====================================================
        // GUARDAR CAMBIOS
        // =====================================================

        $sell->save();


        return redirect()
            ->route('admin.orders')
            ->with(
                'success',
                'Estado del pedido actualizado correctamente.'
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
        return redirect()->route('home');
    }

    $sell = Sell::with([
        'direction',
        'payment',
        'cart.producto_cart.producto'
    ])
    ->where('clientID', $client->clientID)
    ->findOrFail($id);

    return view(
        'client.purchases.show',
        compact('sell')
    );
}
}
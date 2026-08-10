@extends('client.layout')

@section('title', 'Detalle del pedido')

@section('content')

<a href="{{ route('purchases.mine') }}" class="back-link">
    ← Volver a mis compras
</a>

<h1 class="page-title">
    Pedido MB-{{ str_pad($sell->sellID, 6, '0', STR_PAD_LEFT) }}
</h1>

<p class="page-subtitle">
    Realizado el {{ $sell->created_at->format('d/m/Y H:i') }}
</p>

<div class="card">

    <h3>Estado del pedido</h3>

    @php

        $steps = $sell->delivery_method == 'delivery'
            ? [
                'PENDIENTE',
                'PREPARANDO',
                'ENVIADO',
                'ENTREGADO'
            ]
            : [
                'PENDIENTE',
                'PREPARANDO',
                'LISTO PARA RECOGER',
                'ENTREGADO'
            ];

        $current = array_search(
            $sell->order_status,
            $steps
        );

    @endphp

    <div class="status-track">

        @foreach($steps as $index => $step)

            <div class="track-step">

                <div
                    class="track-circle {{ $index <= $current ? 'track-active' : '' }}">
                </div>

                <small>

                    {{ $step }}

                </small>

            </div>

        @endforeach

    </div>

</div>

<div class="card">

    <h3>Información del pedido</h3>

    <div class="info-grid">

        <div class="info-box">

            <strong>Método de entrega</strong>

            <p style="margin-top:10px;">

                {{ $sell->delivery_method == 'delivery'
                    ? 'Envío a domicilio'
                    : 'Recoger en barbería'
                }}

            </p>

        </div>

        <div class="info-box">

            <strong>Método de pago</strong>

            <p style="margin-top:10px;">

                {{ $sell->payment?->paymentMethod ?? '-' }}

            </p>

        </div>

        <div class="info-box">

            <strong>Estado del pago</strong>

            <p style="margin-top:10px;">

                {{ $sell->payment?->status ?? '-' }}

            </p>

        </div>

        <div class="info-box">

            <strong>Guía / Código</strong>

            <p style="margin-top:10px;">

                {{ $sell->tracking_code ?? 'Pendiente de generar' }}

            </p>

        </div>

    </div>

</div>

@if($sell->direction)

<div class="card">

    <h3>Dirección de entrega</h3>

    <br>

    <strong>{{ $sell->direction->name }}</strong>

    <p style="margin-top:10px;">

        {{ $sell->direction->residence }}

    </p>

    <p>

        {{ $sell->direction->city }},
        {{ $sell->direction->state }}

    </p>

    <p>

        C.P. {{ $sell->direction->postal_code }}

    </p>

    @if($sell->direction->description)

        <p style="margin-top:10px;">

            {{ $sell->direction->description }}

        </p>

    @endif

</div>

@endif

<div class="card">

    <h3>Productos</h3>

    <br>

    @foreach($sell->cart->producto_cart as $item)

        <div class="product-item">

            <div>

                <strong>

                    {{ $item->producto->name }}

                </strong>

                <br>

                <small>

                    Cantidad:
                    {{ $item->quantity }}

                </small>

            </div>

            <div>

                <strong>

                    ${{ number_format($item->subtotal,2) }}

                </strong>

            </div>

        </div>

    @endforeach

</div>

<div class="card">

    <div
        style="
            display:flex;
            justify-content:space-between;
            margin-bottom:15px;
        "
    >

        <span>

            Subtotal

        </span>

        <strong>

            ${{ number_format($sell->total - $sell->iva,2) }}

        </strong>

    </div>

    <div
        style="
            display:flex;
            justify-content:space-between;
            margin-bottom:15px;
        "
    >

        <span>

            IVA

        </span>

        <strong>

            ${{ number_format($sell->iva,2) }}

        </strong>

    </div>

    <hr style="margin:20px 0;">

    <div
        style="
            display:flex;
            justify-content:space-between;
            align-items:center;
        "
    >

        <strong style="font-size:22px;">

            Total pagado

        </strong>

        <strong
            style="
                font-size:24px;
                color:#a3352a;
            "
        >

            ${{ number_format($sell->total,2) }}

        </strong>

    </div>

</div>

@endsection
@extends('client.layout')

@section('title', 'Mis compras')

@section('content')

<div class="page-header">

    <h1 class="page-title">
        Mis compras
    </h1>

    <p class="page-subtitle">
        Consulta el historial de tus pedidos.
    </p>

</div>

@forelse($sells as $sell)

<div class="card">

    <div class="purchase-header">

        <div>

            <h2>
                Pedido MB-{{ str_pad($sell->sellID, 6, '0', STR_PAD_LEFT) }}
            </h2>

            <p style="color:#6b5c46; margin-top:6px;">
                {{ $sell->created_at->format('d/m/Y H:i') }}
            </p>

        </div>

        <span class="status-badge">
            {{ $sell->order_status }}
        </span>

    </div>

    <hr style="margin:20px 0;">

    <div class="purchase-footer">

        <div>

            <strong>Total</strong>

            <p class="purchase-total" style="margin-top:5px;">
                ${{ number_format($sell->total,2) }}
            </p>

        </div>

        <a
            href="{{ route('purchases.show', $sell->sellID) }}"
            class="btn btn-primary"
        >
            Ver detalles
        </a>

    </div>

</div>

@empty

<div class="card">

    <h2>No has realizado ninguna compra.</h2>

    <p style="margin-top:10px;">
        Cuando realices una compra aparecerá aquí.
    </p>

</div>

@endforelse

<div style="margin-top:30px;">

    {{ $sells->links() }}

</div>

@endsection

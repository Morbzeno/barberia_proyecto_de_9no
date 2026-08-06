@extends('admin.layout')

@section('title', 'Detalle de venta')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl border border-black/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-black/10">
            <h2 class="font-display text-lg">Productos vendidos</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-[#f4f1ea] text-left text-[#6b5c46]">
                <tr>
                    <th class="px-5 py-3 font-medium">Producto</th>
                    <th class="px-5 py-3 font-medium">Cantidad</th>
                    <th class="px-5 py-3 font-medium">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse ($sell->cart?->producto_cart ?? [] as $item)
                    <tr>
                        <td class="px-5 py-3">{{ $item->producto?->name ?? '—' }}</td>
                        <td class="px-5 py-3">{{ $item->quantity }}</td>
                        <td class="px-5 py-3">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-6 text-center text-[#6b5c46]">Sin productos asociados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl border border-black/10 p-5 space-y-3 text-sm">
        <h2 class="font-display text-lg mb-2">Resumen</h2>
        <p><span class="text-[#6b5c46]">Cliente:</span> {{ $sell->client?->person?->name ?? '—' }} {{ $sell->client?->person?->last_name ?? '' }}</p>
        <p><span class="text-[#6b5c46]">Dirección:</span> {{ $sell->direction?->name ?? '—' }}</p>
        <p><span class="text-[#6b5c46]">Método de pago:</span> {{ $sell->purchase_method ?? '—' }}</p>
        <p><span class="text-[#6b5c46]">IVA:</span> ${{ number_format($sell->iva, 2) }}</p>
        <p class="text-lg font-semibold"><span class="text-[#6b5c46] font-normal text-sm">Total:</span> ${{ number_format($sell->total, 2) }}</p>
        <p><span class="text-[#6b5c46]">Fecha:</span> {{ $sell->created_at->format('d/m/Y H:i') }}</p>
        <a href="{{ route('admin.sells.index') }}" class="inline-block mt-2 text-sm px-4 py-2 rounded-lg border border-black/15 hover:bg-[#f4f1ea]">← Volver</a>
    </div>
</div>
@endsection

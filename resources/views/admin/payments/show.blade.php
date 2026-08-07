@extends('admin.layout')

@section('title', 'Detalle de pago')

@section('content')
<div class="bg-white rounded-xl border border-black/10 p-6 max-w-xl space-y-3 text-sm">
    <p><span class="text-[#6b5c46]">Cliente:</span> {{ $payment->appointment?->client?->person?->name ?? '—' }} {{ $payment->appointment?->client?->person?->last_name ?? '' }}</p>
    <p><span class="text-[#6b5c46]">Barbero:</span> {{ $payment->appointment?->employee?->person?->name ?? '—' }}</p>
    <p><span class="text-[#6b5c46]">Silla:</span> {{ $payment->appointment?->chair?->chairName ?? '—' }}</p>
    <p><span class="text-[#6b5c46]">Método de pago:</span> {{ $payment->paymentMethod }}</p>
    <p class="text-lg font-semibold"><span class="text-[#6b5c46] font-normal text-sm">Subtotal:</span> ${{ number_format($payment->subtotal, 2) }}</p>
    <p><span class="text-[#6b5c46]">Fecha:</span> {{ $payment->created_at->format('d/m/Y H:i') }}</p>
    <a href="{{ route('admin.payments.index') }}" class="inline-block mt-2 text-sm px-4 py-2 rounded-lg border border-black/15 hover:bg-[#f4f1ea]">← Volver</a>
</div>
@endsection

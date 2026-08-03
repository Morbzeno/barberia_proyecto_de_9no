@extends('admin.layout')

@section('title', 'Resumen')
@section('subtitle', 'Vista general del negocio')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
        $cards = [
            ['label' => 'Citas hoy', 'value' => $stats['appointmentsToday'], 'accent' => 'bg-[#d9a862]'],
            ['label' => 'Citas pendientes', 'value' => $stats['appointmentsPending'], 'accent' => 'bg-[#77232c]'],
            ['label' => 'Ventas este mes', 'value' => '$' . number_format($stats['salesThisMonth'], 2), 'accent' => 'bg-[#5fbf83]'],
            ['label' => 'Ventas realizadas', 'value' => $stats['salesCountThisMonth'], 'accent' => 'bg-[#22190f]'],
            ['label' => 'Clientes', 'value' => $stats['clients'], 'accent' => 'bg-[#d9a862]'],
            ['label' => 'Empleados', 'value' => $stats['employees'], 'accent' => 'bg-[#77232c]'],
            ['label' => 'Productos', 'value' => $stats['products'], 'accent' => 'bg-[#22190f]'],
            ['label' => 'Stock bajo (≤5)', 'value' => $stats['lowStockProducts'], 'accent' => 'bg-[#e2685a]'],
        ];
    @endphp

    @foreach ($cards as $card)
        <div class="bg-white rounded-xl border border-black/10 p-5">
            <span class="inline-block w-2 h-2 rounded-full {{ $card['accent'] }} mb-3"></span>
            <p class="text-2xl font-semibold">{{ $card['value'] }}</p>
            <p class="text-sm text-[#6b5c46]">{{ $card['label'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl border border-black/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-black/10 flex items-center justify-between">
            <h2 class="font-display text-lg">Próximas citas</h2>
            <a href="{{ route('admin.appointments.index') }}" class="text-sm text-[#a3352a] hover:underline">Ver todas</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-[#f4f1ea] text-left text-[#6b5c46]">
                <tr>
                    <th class="px-5 py-2 font-medium">Cliente</th>
                    <th class="px-5 py-2 font-medium">Barbero</th>
                    <th class="px-5 py-2 font-medium">Silla</th>
                    <th class="px-5 py-2 font-medium">Fecha</th>
                    <th class="px-5 py-2 font-medium">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/5">
                @forelse ($upcomingAppointments as $appt)
                    <tr>
                        <td class="px-5 py-3">{{ $appt->client?->person?->name }} {{ $appt->client?->person?->last_name }}</td>
                        <td class="px-5 py-3">{{ $appt->employee?->person?->name }}</td>
                        <td class="px-5 py-3">{{ $appt->chair?->chairName }}</td>
                        <td class="px-5 py-3">{{ \Illuminate\Support\Carbon::parse($appt->startHour)->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs bg-[#f4f1ea]">{{ $appt->status }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-6 text-center text-[#6b5c46]">No hay citas próximas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl border border-black/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-black/10">
            <h2 class="font-display text-lg">Stock bajo</h2>
        </div>
        <ul class="divide-y divide-black/5 text-sm">
            @forelse ($lowStock as $product)
                <li class="px-5 py-3 flex items-center justify-between">
                    <span>{{ $product->name }}</span>
                    <span class="text-[#e2685a] font-semibold">{{ $product->stock }} u.</span>
                </li>
            @empty
                <li class="px-5 py-6 text-center text-[#6b5c46]">Todo el inventario está saludable.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="bg-white rounded-xl border border-black/10 overflow-hidden mt-6">
    <div class="px-5 py-4 border-b border-black/10 flex items-center justify-between">
        <h2 class="font-display text-lg">Ventas recientes</h2>
        <a href="{{ route('admin.sells.index') }}" class="text-sm text-[#a3352a] hover:underline">Ver todas</a>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-[#f4f1ea] text-left text-[#6b5c46]">
            <tr>
                <th class="px-5 py-2 font-medium">Cliente</th>
                <th class="px-5 py-2 font-medium">Método</th>
                <th class="px-5 py-2 font-medium">Total</th>
                <th class="px-5 py-2 font-medium">Fecha</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse ($recentSells as $sell)
                <tr>
                    <td class="px-5 py-3">{{ $sell->client?->person?->name ?? '—' }}</td>
                    <td class="px-5 py-3">{{ $sell->purchase_method ?? '—' }}</td>
                    <td class="px-5 py-3">${{ number_format($sell->total, 2) }}</td>
                    <td class="px-5 py-3">{{ $sell->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-6 text-center text-[#6b5c46]">Aún no hay ventas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

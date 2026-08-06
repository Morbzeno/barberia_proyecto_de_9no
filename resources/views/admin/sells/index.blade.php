@extends('admin.layout')

@section('title', 'Ventas')
@section('subtitle', 'Historial de ventas de productos')

@section('content')
<div class="bg-white rounded-xl border border-black/10 overflow-hidden">
    <div class="p-4 border-b border-black/10">
        <form method="GET" class="flex gap-2">
            <input type="date" name="date" value="{{ request('date') }}" class="border border-black/15 rounded-lg px-3 py-2 text-sm">
            <button class="text-sm px-4 py-2 rounded-lg border border-black/15 hover:bg-[#f4f1ea]">Filtrar</button>
            @if (request('date'))
                <a href="{{ route('admin.sells.index') }}" class="text-sm px-4 py-2 rounded-lg text-[#a3352a] hover:underline self-center">Limpiar</a>
            @endif
        </form>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-[#f4f1ea] text-left text-[#6b5c46]">
            <tr>
                <th class="px-5 py-3 font-medium">Cliente</th>
                <th class="px-5 py-3 font-medium">Método de pago</th>
                <th class="px-5 py-3 font-medium">IVA</th>
                <th class="px-5 py-3 font-medium">Total</th>
                <th class="px-5 py-3 font-medium">Fecha</th>
                <th class="px-5 py-3 font-medium text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse ($sells as $sell)
                <tr>
                    <td class="px-5 py-3 font-medium">{{ $sell->client?->person?->name ?? '—' }} {{ $sell->client?->person?->last_name ?? '' }}</td>
                    <td class="px-5 py-3">{{ $sell->purchase_method ?? '—' }}</td>
                    <td class="px-5 py-3">${{ number_format($sell->iva, 2) }}</td>
                    <td class="px-5 py-3 font-semibold">${{ number_format($sell->total, 2) }}</td>
                    <td class="px-5 py-3">{{ $sell->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.sells.show', $sell) }}" class="text-[#a3352a] hover:underline">Ver</a>
                            @include('admin.partials.delete-button', ['action' => route('admin.sells.destroy', $sell)])
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-[#6b5c46]">Aún no hay ventas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4">{{ $sells->links() }}</div>
</div>
@endsection

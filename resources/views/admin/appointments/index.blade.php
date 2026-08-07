@extends('admin.layout')

@section('title', 'Citas')
@section('subtitle', 'Agenda de citas de clientes')

@section('header-actions')
    <a href="{{ route('admin.appointments.create') }}" class="bg-[#22190f] text-white text-sm px-4 py-2 rounded-lg hover:bg-[#3a2c1a]">+ Nueva cita</a>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-black/10 overflow-hidden">
    <div class="p-4 border-b border-black/10">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="date" name="date" value="{{ request('date') }}"
                   class="border border-black/15 rounded-lg px-3 py-2 text-sm">
            <select name="status" class="border border-black/15 rounded-lg px-3 py-2 text-sm">
                <option value="">Todos los estados</option>
                @foreach (['pending' => 'Pendiente', 'in_process' => 'En proceso', 'Finished' => 'Finalizada', 'cancelled' => 'Cancelada'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="text-sm px-4 py-2 rounded-lg border border-black/15 hover:bg-[#f4f1ea]">Filtrar</button>
            @if (request('date') || request('status'))
                <a href="{{ route('admin.appointments.index') }}" class="text-sm px-4 py-2 rounded-lg text-[#a3352a] hover:underline self-center">Limpiar</a>
            @endif
        </form>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-[#f4f1ea] text-left text-[#6b5c46]">
            <tr>
                <th class="px-5 py-3 font-medium">Cliente</th>
                <th class="px-5 py-3 font-medium">Barbero</th>
                <th class="px-5 py-3 font-medium">Silla</th>
                <th class="px-5 py-3 font-medium">Inicio</th>
                <th class="px-5 py-3 font-medium">Fin</th>
                <th class="px-5 py-3 font-medium">Estado</th>
                <th class="px-5 py-3 font-medium text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse ($appointments as $appt)
                @php
                    $statusColors = [
                        'pending' => 'bg-[#d9a862]/20 text-[#8a5f1f]',
                        'in_process' => 'bg-[#5f8fbf]/15 text-[#2f5a8b]',
                        'Finished' => 'bg-[#5fbf83]/15 text-[#2f6b45]',
                        'cancelled' => 'bg-[#e2685a]/15 text-[#a3352a]',
                    ];
                @endphp
                <tr>
                    <td class="px-5 py-3 font-medium">{{ $appt->client?->person?->name }} {{ $appt->client?->person?->last_name }}</td>
                    <td class="px-5 py-3">{{ $appt->employee?->person?->name }}</td>
                    <td class="px-5 py-3">{{ $appt->chair?->chairName }}</td>
                    <td class="px-5 py-3">{{ \Illuminate\Support\Carbon::parse($appt->startHour)->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3">{{ \Illuminate\Support\Carbon::parse($appt->finishHour)->format('H:i') }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $statusColors[$appt->status] ?? 'bg-black/5' }}">{{ $appt->status }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.appointments.edit', $appt) }}" class="text-[#a3352a] hover:underline">Editar</a>
                            @include('admin.partials.delete-button', ['action' => route('admin.appointments.destroy', $appt)])
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-5 py-8 text-center text-[#6b5c46]">No hay citas registradas todavía.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4">{{ $appointments->links() }}</div>
</div>
@endsection

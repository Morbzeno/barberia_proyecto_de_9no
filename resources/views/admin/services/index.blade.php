@extends('admin.layout')

@section('title', 'Servicios')
@section('subtitle', 'Servicios de barbería disponibles para agendar')

@section('header-actions')
    <a href="{{ route('admin.services.create') }}" class="bg-[#22190f] text-white text-sm px-4 py-2 rounded-lg hover:bg-[#3a2c1a]">+ Nuevo servicio</a>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-black/10 overflow-hidden">
    <div class="p-4 border-b border-black/10">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar servicio..."
                   class="border border-black/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d9a862]">
            <button class="text-sm px-4 py-2 rounded-lg border border-black/15 hover:bg-[#f4f1ea]">Buscar</button>
        </form>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-[#f4f1ea] text-left text-[#6b5c46]">
            <tr>
                <th class="px-5 py-3 font-medium">Servicio</th>
                <th class="px-5 py-3 font-medium">Descripción</th>
                <th class="px-5 py-3 font-medium">Precio</th>
                <th class="px-5 py-3 font-medium">Duración aprox.</th>
                <th class="px-5 py-3 font-medium text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse ($services as $service)
                <tr>
                    <td class="px-5 py-3 font-medium">{{ $service->name }}</td>
                    <td class="px-5 py-3 text-[#6b5c46]">{{ Str::limit($service->description, 60) }}</td>
                    <td class="px-5 py-3">${{ number_format($service->price, 2) }}</td>
                    <td class="px-5 py-3">{{ $service->aproxDuration }} min</td>
                    <td class="px-5 py-3">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.services.edit', $service) }}" class="text-[#a3352a] hover:underline">Editar</a>
                            @include('admin.partials.delete-button', ['action' => route('admin.services.destroy', $service)])
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-[#6b5c46]">No hay servicios registrados todavía.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4">{{ $services->links() }}</div>
</div>
@endsection

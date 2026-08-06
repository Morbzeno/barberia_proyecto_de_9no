@extends('admin.layout')

@section('title', 'Sillas')
@section('subtitle', 'Puestos de trabajo disponibles y sus servicios')

@section('header-actions')
    <a href="{{ route('admin.chairs.create') }}" class="bg-[#22190f] text-white text-sm px-4 py-2 rounded-lg hover:bg-[#3a2c1a]">+ Nueva silla</a>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-black/10 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-[#f4f1ea] text-left text-[#6b5c46]">
            <tr>
                <th class="px-5 py-3 font-medium">Silla</th>
                <th class="px-5 py-3 font-medium">Servicios asignados</th>
                <th class="px-5 py-3 font-medium text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse ($chairs as $chair)
                <tr>
                    <td class="px-5 py-3 font-medium">{{ $chair->chairName }}</td>
                    <td class="px-5 py-3 text-[#6b5c46]">
                        @forelse ($chair->services as $service)
                            <span class="inline-block bg-[#f4f1ea] rounded-full px-2 py-0.5 text-xs mr-1 mb-1">{{ $service->name }}</span>
                        @empty
                            —
                        @endforelse
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.chairs.edit', $chair) }}" class="text-[#a3352a] hover:underline">Editar</a>
                            @include('admin.partials.delete-button', ['action' => route('admin.chairs.destroy', $chair)])
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-5 py-8 text-center text-[#6b5c46]">No hay sillas registradas todavía.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4">{{ $chairs->links() }}</div>
</div>
@endsection

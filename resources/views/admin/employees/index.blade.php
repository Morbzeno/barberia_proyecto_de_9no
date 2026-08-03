@extends('admin.layout')

@section('title', 'Empleados')
@section('subtitle', 'Barberos y administradores del negocio')

@section('header-actions')
    <a href="{{ route('admin.employees.create') }}" class="bg-[#22190f] text-white text-sm px-4 py-2 rounded-lg hover:bg-[#3a2c1a]">+ Nuevo empleado</a>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-black/10 overflow-hidden">
    <div class="p-4 border-b border-black/10">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre..."
                   class="border border-black/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d9a862]">
            <button class="text-sm px-4 py-2 rounded-lg border border-black/15 hover:bg-[#f4f1ea]">Buscar</button>
        </form>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-[#f4f1ea] text-left text-[#6b5c46]">
            <tr>
                <th class="px-5 py-3 font-medium">Nombre</th>
                <th class="px-5 py-3 font-medium">Correo</th>
                <th class="px-5 py-3 font-medium">Teléfono</th>
                <th class="px-5 py-3 font-medium">Rol</th>
                <th class="px-5 py-3 font-medium">Pago</th>
                <th class="px-5 py-3 font-medium text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse ($employees as $employee)
                <tr>
                    <td class="px-5 py-3 font-medium">{{ $employee->person?->name }} {{ $employee->person?->last_name }}</td>
                    <td class="px-5 py-3 text-[#6b5c46]">{{ $employee->user?->email }}</td>
                    <td class="px-5 py-3">{{ $employee->person?->phone_number }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $employee->admin_type === 'admin' ? 'bg-[#d9a862]/20 text-[#8a5f1f]' : 'bg-black/5 text-[#6b5c46]' }}">
                            {{ $employee->admin_type === 'admin' ? 'Administrador' : 'Barbero' }}
                        </span>
                    </td>
                    <td class="px-5 py-3">${{ number_format($employee->payment, 2) }}</td>
                    <td class="px-5 py-3">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.employees.edit', $employee) }}" class="text-[#a3352a] hover:underline">Editar</a>
                            @include('admin.partials.delete-button', ['action' => route('admin.employees.destroy', $employee)])
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-[#6b5c46]">No hay empleados registrados todavía.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4">{{ $employees->links() }}</div>
</div>
@endsection

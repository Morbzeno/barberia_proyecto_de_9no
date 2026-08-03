@extends('admin.layout')

@section('title', 'Categorías')
@section('subtitle', 'Organiza los productos de la tienda por categoría')

@section('header-actions')
    <a href="{{ route('admin.categories.create') }}" class="bg-[#22190f] text-white text-sm px-4 py-2 rounded-lg hover:bg-[#3a2c1a]">+ Nueva categoría</a>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-black/10 overflow-hidden">
    <div class="p-4 border-b border-black/10">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre..."
                   class="flex-1 max-w-xs border border-black/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d9a862]">
            <button class="text-sm px-4 py-2 rounded-lg border border-black/15 hover:bg-[#f4f1ea]">Buscar</button>
        </form>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-[#f4f1ea] text-left text-[#6b5c46]">
            <tr>
                <th class="px-5 py-3 font-medium">Nombre</th>
                <th class="px-5 py-3 font-medium">Etiquetas</th>
                <th class="px-5 py-3 font-medium">Productos</th>
                <th class="px-5 py-3 font-medium text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse ($categories as $category)
                <tr>
                    <td class="px-5 py-3 font-medium">{{ $category->name }}</td>
                    <td class="px-5 py-3 text-[#6b5c46]">
                        @foreach (($category->tags ?? []) as $tag)
                            <span class="inline-block bg-[#f4f1ea] rounded-full px-2 py-0.5 text-xs mr-1">{{ $tag }}</span>
                        @endforeach
                    </td>
                    <td class="px-5 py-3">{{ $category->products_count }}</td>
                    <td class="px-5 py-3">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-[#a3352a] hover:underline">Editar</a>
                            @include('admin.partials.delete-button', ['action' => route('admin.categories.destroy', $category)])
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-8 text-center text-[#6b5c46]">No hay categorías registradas todavía.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4">{{ $categories->links() }}</div>
</div>
@endsection

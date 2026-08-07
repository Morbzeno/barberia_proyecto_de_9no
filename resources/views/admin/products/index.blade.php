@extends('admin.layout')

@section('title', 'Productos')
@section('subtitle', 'Catálogo e inventario de la tienda')

@section('header-actions')
    <a href="{{ route('admin.products.create') }}" class="bg-[#22190f] text-white text-sm px-4 py-2 rounded-lg hover:bg-[#3a2c1a]">+ Nuevo producto</a>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-black/10 overflow-hidden">
    <div class="p-4 border-b border-black/10">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre..."
                   class="border border-black/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d9a862]">
            <select name="category" class="border border-black/15 rounded-lg px-3 py-2 text-sm">
                <option value="">Todas las categorías</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->categoryID }}" @selected(request('category') == $category->categoryID)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="text-sm px-4 py-2 rounded-lg border border-black/15 hover:bg-[#f4f1ea]">Filtrar</button>
        </form>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-[#f4f1ea] text-left text-[#6b5c46]">
            <tr>
                <th class="px-5 py-3 font-medium">Producto</th>
                <th class="px-5 py-3 font-medium">Categoría</th>
                <th class="px-5 py-3 font-medium">Precio venta</th>
                <th class="px-5 py-3 font-medium">Stock</th>
                <th class="px-5 py-3 font-medium">Estado</th>
                <th class="px-5 py-3 font-medium text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black/5">
            @forelse ($products as $product)
                <tr>
                    <td class="px-5 py-3 font-medium">{{ $product->name }}</td>
                    <td class="px-5 py-3 text-[#6b5c46]">{{ $product->category?->name ?? '—' }}</td>
                    <td class="px-5 py-3">${{ number_format($product->sell_price, 2) }}</td>
                    <td class="px-5 py-3 {{ $product->stock <= 5 ? 'text-[#a3352a] font-semibold' : '' }}">{{ $product->stock }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $product->state === 'ACTIVO' ? 'bg-[#5fbf83]/15 text-[#2f6b45]' : 'bg-black/5 text-[#6b5c46]' }}">
                            {{ $product->state }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-[#a3352a] hover:underline">Editar</a>
                            @include('admin.partials.delete-button', ['action' => route('admin.products.destroy', $product)])
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-[#6b5c46]">No hay productos registrados todavía.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4">{{ $products->links() }}</div>
</div>
@endsection

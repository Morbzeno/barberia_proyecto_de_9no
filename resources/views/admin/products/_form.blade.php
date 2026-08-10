@php $product = $product ?? null; @endphp

<div class="grid sm:grid-cols-2 gap-5">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Nombre</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d9a862]" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Categoría</label>
        <select name="categoryID" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
            <option value="">Selecciona una categoría</option>
            @foreach ($categories as $category)
                <option value="{{ $category->categoryID }}" @selected(old('categoryID', $product->categoryID ?? '') == $category->categoryID)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Código de barras</label>
        <input type="number" name="bar_code" value="{{ old('bar_code', $product->bar_code ?? '') }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Precio de venta</label>
        <input type="number" step="0.01" name="sell_price" value="{{ old('sell_price', $product->sell_price ?? '') }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Precio de compra</label>
        <input type="number" step="0.01" name="buy_price" value="{{ old('buy_price', $product->buy_price ?? '') }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Precio mayoreo (opcional)</label>
        <input type="number" step="0.01" name="wholesale_price" value="{{ old('wholesale_price', $product->wholesale_price ?? '') }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Stock</label>
        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Estado</label>
        <select name="state" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
            <option value="ACTIVO" @selected(old('state', $product->state ?? 'ACTIVO') === 'ACTIVO')>Activo</option>
            <option value="INACTIVO" @selected(old('state', $product->state ?? '') === 'INACTIVO')>Inactivo</option>
        </select>
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Descripción</label>
        <textarea name="description" rows="3"
                  class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
    <div class="sm:col-span-2">
    <label class="block text-sm font-medium mb-1">
        Imágenes del producto
    </label>

    <input
        type="file"
        name="images[]"
        id="productImages"
        accept="image/jpeg,image/png,image/jpg,image/webp"
        multiple
        class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm
               file:mr-4 file:rounded-md file:border-0
               file:bg-[#22190f] file:px-4 file:py-2
               file:text-sm file:text-white
               hover:file:bg-[#3a2c1a]"
    >

    <p class="text-xs text-[#6b5c46] mt-2">
        Puedes seleccionar una o varias imágenes. Formatos: JPG, PNG o WEBP.
    </p>
</div>
</div>

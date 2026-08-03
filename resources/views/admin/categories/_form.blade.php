@php $category = $category ?? null; @endphp

<div>
    <label class="block text-sm font-medium mb-1">Nombre</label>
    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
           class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d9a862]" required>
</div>

<div>
    <label class="block text-sm font-medium mb-1">Descripción</label>
    <textarea name="description" rows="3"
              class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d9a862]" required>{{ old('description', $category->description ?? '') }}</textarea>
</div>

<div>
    <label class="block text-sm font-medium mb-1">Etiquetas (separadas por coma)</label>
    <input type="text" name="tags" value="{{ old('tags', isset($category) ? implode(', ', $category->tags ?? []) : '') }}"
           placeholder="cuidado capilar, barba, promoción"
           class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d9a862]">
</div>

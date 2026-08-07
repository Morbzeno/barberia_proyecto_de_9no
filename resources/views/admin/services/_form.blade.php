@php $service = $service ?? null; @endphp

<div class="grid sm:grid-cols-2 gap-5">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Nombre</label>
        <input type="text" name="name" value="{{ old('name', $service->name ?? '') }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d9a862]" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Precio</label>
        <input type="number" step="0.01" name="price" value="{{ old('price', $service->price ?? '') }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Duración aproximada (minutos)</label>
        <input type="number" name="aproxDuration" value="{{ old('aproxDuration', $service->aproxDuration ?? '') }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Descripción</label>
        <textarea name="description" rows="3"
                  class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>{{ old('description', $service->description ?? '') }}</textarea>
    </div>
</div>

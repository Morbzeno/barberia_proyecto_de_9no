@php $chair = $chair ?? null; $selected = $chair ? $chair->services->pluck('serviceID')->all() : old('services', []); @endphp

<div>
    <label class="block text-sm font-medium mb-1">Nombre de la silla</label>
    <input type="text" name="chairName" value="{{ old('chairName', $chair->chairName ?? '') }}"
           class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#d9a862]" required>
</div>

<div>
    <label class="block text-sm font-medium mb-2">Servicios que puede atender</label>
    <div class="grid sm:grid-cols-2 gap-2 border border-black/15 rounded-lg p-4">
        @forelse ($services as $service)
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="services[]" value="{{ $service->serviceID }}"
                       @checked(in_array($service->serviceID, $selected))>
                {{ $service->name }}
            </label>
        @empty
            <p class="text-sm text-[#6b5c46]">Primero registra servicios para poder asignarlos.</p>
        @endforelse
    </div>
</div>

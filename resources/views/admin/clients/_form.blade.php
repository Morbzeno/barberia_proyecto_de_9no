@php $client = $client ?? null; @endphp

<div class="grid sm:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-medium mb-1">Correo electrónico</label>
        <input type="email" name="email" value="{{ old('email', $client->user->email ?? '') }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>
    <div></div>
    <div>
        <label class="block text-sm font-medium mb-1">Contraseña {{ $client ? '(dejar en blanco para no cambiar)' : '' }}</label>
        <input type="password" name="password" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" {{ $client ? '' : 'required' }}>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Nombre</label>
        <input type="text" name="name" value="{{ old('name', $client->person->name ?? '') }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Apellido</label>
        <input type="text" name="last_name" value="{{ old('last_name', $client->person->last_name ?? '') }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Teléfono</label>
        <input type="text" name="phone_number" value="{{ old('phone_number', $client->person->phone_number ?? '') }}"
               maxlength="10" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>
</div>

@php
    $employee = $employee ?? null;
    $scheduleByDay = collect($employee->schedule ?? [])->keyBy('day');
@endphp

<div class="grid sm:grid-cols-2 gap-5">
    <div>
        <h3 class="font-display text-lg mb-3">Cuenta de acceso</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email', $employee->user->email ?? '') }}"
                       class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Contraseña {{ $employee ? '(dejar en blanco para no cambiar)' : '' }}</label>
                <input type="password" name="password" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" {{ $employee ? '' : 'required' }}>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
    </div>

    <div>
        <h3 class="font-display text-lg mb-3">Datos personales</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nombre</label>
                <input type="text" name="name" value="{{ old('name', $employee->person->name ?? '') }}"
                       class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Apellido</label>
                <input type="text" name="last_name" value="{{ old('last_name', $employee->person->last_name ?? '') }}"
                       class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Teléfono</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $employee->person->phone_number ?? '') }}"
                       maxlength="10" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">RFC</label>
        <input type="text" name="rfc" value="{{ old('rfc', $employee->rfc ?? '') }}"
               maxlength="13" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Pago (sueldo)</label>
        <input type="number" step="0.01" name="payment" value="{{ old('payment', $employee->payment ?? '') }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Rol</label>
        <select name="admin_type" class="w-full sm:w-64 border border-black/15 rounded-lg px-3 py-2 text-sm" required>
            <option value="barber" @selected(old('admin_type', $employee->admin_type ?? 'barber') === 'barber')>Barbero</option>
            <option value="admin" @selected(old('admin_type', $employee->admin_type ?? '') === 'admin')>Administrador</option>
        </select>
    </div>
</div>

<div>
    <h3 class="font-display text-lg mb-3">Horario laboral</h3>
    <div class="border border-black/15 rounded-lg divide-y divide-black/10">
        @foreach ($days as $day)
            @php $current = $scheduleByDay->get($day); @endphp
            <div class="flex flex-wrap items-center gap-4 px-4 py-3">
                <label class="flex items-center gap-2 w-32 text-sm capitalize">
                    <input type="checkbox" name="days[]" value="{{ $day }}" @checked(in_array($day, old('days', $scheduleByDay->keys()->all())))>
                    {{ $day }}
                </label>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-[#6b5c46]">De</span>
                    <input type="time" name="start_time[{{ $day }}]" value="{{ old('start_time.'.$day, $current['start'] ?? '09:00') }}"
                           class="border border-black/15 rounded-lg px-2 py-1">
                    <span class="text-[#6b5c46]">a</span>
                    <input type="time" name="end_time[{{ $day }}]" value="{{ old('end_time.'.$day, $current['end'] ?? '18:00') }}"
                           class="border border-black/15 rounded-lg px-2 py-1">
                </div>
            </div>
        @endforeach
    </div>
</div>

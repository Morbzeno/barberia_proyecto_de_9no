@php
    $appointment = $appointment ?? null;
    $toInput = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d\TH:i') : '';
@endphp

<div class="grid sm:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-medium mb-1">Cliente</label>
        <select name="clientID" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
            <option value="">Selecciona un cliente</option>
            @foreach ($clients as $client)
                <option value="{{ $client->clientID }}" @selected(old('clientID', $appointment->clientID ?? '') == $client->clientID)>
                    {{ $client->person?->name }} {{ $client->person?->last_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Barbero</label>
        <select name="employeeID" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
            <option value="">Selecciona un barbero</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->employeeID }}" @selected(old('employeeID', $appointment->employeeID ?? '') == $employee->employeeID)>
                    {{ $employee->person?->name }} {{ $employee->person?->last_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Silla</label>
        <select name="chairID" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
            <option value="">Selecciona una silla</option>
            @foreach ($chairs as $chair)
                <option value="{{ $chair->chairID }}" @selected(old('chairID', $appointment->chairID ?? '') == $chair->chairID)>{{ $chair->chairName }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Estado</label>
        <select name="status" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
            @foreach (['pending' => 'Pendiente', 'in_process' => 'En proceso', 'Finished' => 'Finalizada', 'cancelled' => 'Cancelada'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $appointment->status ?? 'pending') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Inicio</label>
        <input type="datetime-local" name="startHour" value="{{ old('startHour', $toInput($appointment->startHour ?? null)) }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Fin</label>
        <input type="datetime-local" name="finishHour" value="{{ old('finishHour', $toInput($appointment->finishHour ?? null)) }}"
               class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm" required>
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Notas</label>
        <textarea name="notes" rows="3" class="w-full border border-black/15 rounded-lg px-3 py-2 text-sm">{{ old('notes', $appointment->notes ?? '') }}</textarea>
    </div>
</div>

{{-- Espera: $action (url) y opcional $label --}}
<form method="POST" action="{{ $action }}" onsubmit="return confirm('¿Seguro que deseas eliminar este registro? Esta acción no se puede deshacer.');">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-[#a3352a] hover:underline">{{ $label ?? 'Eliminar' }}</button>
</form>

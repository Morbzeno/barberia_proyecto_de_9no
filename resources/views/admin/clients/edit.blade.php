@extends('admin.layout')

@section('title', 'Editar cliente')

@section('content')
<div class="bg-white rounded-xl border border-black/10 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.clients.update', $client) }}" class="space-y-5">
        @csrf
        @method('PUT')
        @include('admin.clients._form')
        <div class="flex gap-3">
            <button class="bg-[#22190f] text-white text-sm px-5 py-2.5 rounded-lg hover:bg-[#3a2c1a]">Guardar cambios</button>
            <a href="{{ route('admin.clients.index') }}" class="text-sm px-5 py-2.5 rounded-lg border border-black/15 hover:bg-[#f4f1ea]">Cancelar</a>
        </div>
    </form>
</div>
@endsection

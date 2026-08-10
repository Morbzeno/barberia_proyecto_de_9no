@extends('admin.layout')

@section('title', 'Nuevo producto')

@section('content')
<div class="bg-white rounded-xl border border-black/10 p-6 max-w-3xl">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @include('admin.products._form')
        <div class="flex gap-3">
            <button class="bg-[#22190f] text-white text-sm px-5 py-2.5 rounded-lg hover:bg-[#3a2c1a]">Guardar producto</button>
            <a href="{{ route('admin.products.index') }}" class="text-sm px-5 py-2.5 rounded-lg border border-black/15 hover:bg-[#f4f1ea]">Cancelar</a>
        </div>
    </form>
</div>
@endsection

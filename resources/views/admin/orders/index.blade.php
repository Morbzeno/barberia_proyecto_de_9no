@extends('admin.layout')

@section('title', 'Pedidos')
@section('subtitle', 'Gestión y seguimiento de pedidos')

@section('content')

<div class="bg-white rounded-xl border border-black/10 overflow-hidden">

    {{-- FILTROS --}}
    <div class="p-4 border-b border-black/10">

        <form method="GET" class="flex gap-2">

            <select
                name="status"
                class="border border-black/15 rounded-lg px-3 py-2 text-sm"
            >
                <option value="">Todos los estados</option>

                <option
                    value="PENDIENTE"
                    @selected(request('status') === 'PENDIENTE')
                >
                    Pendiente
                </option>

                <option
                    value="PREPARANDO"
                    @selected(request('status') === 'PREPARANDO')
                >
                    Preparando
                </option>

                <option
                    value="ENVIADO"
                    @selected(request('status') === 'ENVIADO')
                >
                    Enviado
                </option>

                <option
                    value="LISTO PARA RECOGER"
                    @selected(request('status') === 'LISTO PARA RECOGER')
                >
                    Listo para recoger
                </option>

                <option
                    value="ENTREGADO"
                    @selected(request('status') === 'ENTREGADO')
                >
                    Entregado
                </option>

            </select>


            <button
                class="text-sm px-4 py-2 rounded-lg border border-black/15 hover:bg-[#f4f1ea]"
            >
                Filtrar
            </button>


            @if (request('status'))

                <a
                    href="{{ route('admin.orders') }}"
                    class="text-sm px-4 py-2 rounded-lg text-[#a3352a] hover:underline self-center"
                >
                    Limpiar
                </a>

            @endif

        </form>

    </div>


    {{-- TABLA --}}
    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-[#f4f1ea] text-left text-[#6b5c46]">

                <tr>

                    <th class="px-5 py-3 font-medium">
                        Pedido
                    </th>

                    <th class="px-5 py-3 font-medium">
                        Cliente
                    </th>

                    <th class="px-5 py-3 font-medium">
                        Entrega
                    </th>

                    <th class="px-5 py-3 font-medium">
                        Pago
                    </th>

                    <th class="px-5 py-3 font-medium">
                        Total
                    </th>

                    <th class="px-5 py-3 font-medium">
                        Estado
                    </th>

                    <th class="px-5 py-3 font-medium">
                        Código / Guía
                    </th>

                    <th class="px-5 py-3 font-medium">
                        Fecha
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-black/5">

                @forelse ($sells as $sell)

                    <tr>

                        {{-- CÓDIGO --}}
                        <td class="px-5 py-3 font-semibold">

                            MB-{{ str_pad(
                                $sell->sellID,
                                6,
                                '0',
                                STR_PAD_LEFT
                            ) }}

                        </td>


                        {{-- CLIENTE --}}
                        <td class="px-5 py-3">

                            {{ $sell->client?->person?->name ?? '—' }}

                            {{ $sell->client?->person?->last_name ?? '' }}

                        </td>


                        {{-- ENTREGA --}}
                        <td class="px-5 py-3">

                            @if ($sell->delivery_method === 'delivery')

                                <span class="font-medium">
                                    Envío
                                </span>

                                @if ($sell->direction)

                                    <div class="text-xs text-[#6b5c46] mt-1">

                                        {{ $sell->direction->city }},
                                        {{ $sell->direction->state }}

                                    </div>

                                @endif

                            @else

                                <span class="font-medium">
                                    Recoger
                                </span>

                            @endif

                        </td>


                        {{-- PAGO --}}
                        <td class="px-5 py-3">

                            <div>
                                {{ $sell->purchase_method ?? '—' }}
                            </div>

                            @if ($sell->payment)

                                <div class="text-xs text-green-700">
                                    {{ $sell->payment->status }}
                                </div>

                            @endif

                        </td>


                        {{-- TOTAL --}}
                        <td class="px-5 py-3 font-semibold">

                            ${{ number_format($sell->total, 2) }}

                        </td>


                        {{-- ESTADO --}}
                        <td class="px-5 py-3">

                            <form
                                method="POST"
                                action="{{ route(
                                    'admin.orders.status',
                                    $sell->sellID
                                ) }}"
                            >

                                @csrf
                                @method('PATCH')


                                <select
                                    name="order_status"
                                    onchange="this.form.submit()"
                                    class="border border-black/15 rounded-lg px-2 py-2 text-xs"
                                >

                                    <option
                                        value="PENDIENTE"
                                        @selected(
                                            $sell->order_status === 'PENDIENTE'
                                        )
                                    >
                                        Pendiente
                                    </option>


                                    <option
                                        value="PREPARANDO"
                                        @selected(
                                            $sell->order_status === 'PREPARANDO'
                                        )
                                    >
                                        Preparando
                                    </option>


                                    @if (
                                        $sell->delivery_method === 'delivery'
                                    )

                                        <option
                                            value="ENVIADO"
                                            @selected(
                                                $sell->order_status === 'ENVIADO'
                                            )
                                        >
                                            Enviado
                                        </option>

                                    @else

                                        <option
                                            value="LISTO PARA RECOGER"
                                            @selected(
                                                $sell->order_status ===
                                                'LISTO PARA RECOGER'
                                            )
                                        >
                                            Listo para recoger
                                        </option>

                                    @endif


                                    <option
                                        value="ENTREGADO"
                                        @selected(
                                            $sell->order_status === 'ENTREGADO'
                                        )
                                    >
                                        Entregado
                                    </option>

                                </select>

                            </form>

                        </td>


                        {{-- GUÍA --}}
                        <td class="px-5 py-3">

                            @if ($sell->tracking_code)

                                <span class="text-xs font-medium">
                                    {{ $sell->tracking_code }}
                                </span>

                            @elseif (
                                $sell->delivery_method === 'delivery'
                            )

                                <span class="text-[#6b5c46]">
                                    Pendiente
                                </span>

                            @else

                                <span class="text-[#6b5c46]">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- FECHA --}}
                        <td class="px-5 py-3">

                            {{ $sell->created_at->format('d/m/Y H:i') }}

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="px-5 py-8 text-center text-[#6b5c46]"
                        >
                            Aún no hay pedidos registrados.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <div class="p-4">

        {{ $sells->links() }}

    </div>

</div>

@endsection
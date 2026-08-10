@extends('client.layout')

@section('title', 'Mis citas')

@section('content')

<a href="{{ url('/') }}" class="back-link">
    ← Volver al inicio
</a>

<div class="page-header">

    <h1 class="page-title">
        Mis citas
    </h1>

    <p class="page-subtitle">
        Consulta tus próximas citas y tu historial en Machin Barber.
    </p>

</div>

@forelse($appointments as $appointment)

<div class="card">

    <div
        style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:20px;
        "
    >

        <div>

            <h2>

                {{ $appointment->startHour->format('d/m/Y') }}

            </h2>

            <p style="color:#6b5c46;">

                {{ $appointment->startHour->format('H:i') }}

                -

                {{ $appointment->finishHour->format('H:i') }}

            </p>

        </div>

        <div>

            @switch($appointment->status)

                @case('pending')

                    <span class="status status-pending">
                        Pendiente
                    </span>

                @break


                @case('in_process')

                    <span class="status status-process">
                        En proceso
                    </span>

                @break


                @case('Finished')

                    <span class="status status-finished">
                        Finalizada
                    </span>

                @break


                @case('cancelled')

                    <span class="status status-cancelled">
                        Cancelada
                    </span>

                @break


                @default

                    <span class="status">

                        {{ $appointment->status }}

                    </span>

            @endswitch

        </div>

    </div>

    <hr style="margin:20px 0;">

    <div
        style="
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
            gap:20px;
        "
    >

        <div>

            <strong>Barbero</strong>

            <p>

                {{ $appointment->employee?->person?->name ?? 'No asignado' }}

                {{ $appointment->employee?->person?->last_name ?? '' }}

            </p>

        </div>

        <div>

            <strong>Silla</strong>

            <p>

                {{ $appointment->chair?->chairName ?? '-' }}

            </p>

        </div>

        <div>

            <strong>Pago</strong>

            <p>

                {{ $appointment->payment?->paymentMethod ?? 'Pendiente' }}

            </p>

        </div>

    </div>

    <hr style="margin:20px 0;">

    <h3>

        Servicios

    </h3>

    <br>

    @forelse($appointment->appointmentDetails as $detail)

        <div
            style="
                display:flex;
                justify-content:space-between;
                margin-bottom:10px;
            "
        >

            <span>

                {{ $detail->service?->name ?? 'Servicio' }}

            </span>

            <strong>

                ${{ number_format($detail->totalPrice,2) }}

            </strong>

        </div>

    @empty

        <p>

            No hay servicios registrados.

        </p>

    @endforelse

    <hr style="margin:20px 0;">

    <div
        style="
            display:flex;
            justify-content:space-between;
            align-items:center;
        "
    >

        <strong>

            Total

        </strong>

        <strong
            style="
                color:#a3352a;
                font-size:22px;
            "
        >

            ${{ number_format(
                $appointment->appointmentDetails->sum('totalPrice'),
                2
            ) }}

        </strong>

    </div>

    @if($appointment->notes)

        <hr style="margin:20px 0;">

        <strong>

            Notas

        </strong>

        <p style="margin-top:8px;">

            {{ $appointment->notes }}

        </p>

    @endif

</div>

@empty

<div class="card">

    <h2>

        Todavía no tienes citas.

    </h2>

    <p style="margin-top:10px;">

        Cuando reserves una cita aparecerá aquí.

    </p>

</div>

@endforelse

@endsection
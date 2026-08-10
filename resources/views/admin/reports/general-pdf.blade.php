<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 25px 30px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #26313d;
            font-size: 9px;
            margin: 0;
        }

        .container {
            width: 100%;
        }

        /* =========================
           ENCABEZADO
        ========================= */

        .header {
            border-bottom: 2px solid #26313d;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .header-left {
            float: left;
            width: 55%;
        }

        .header-right {
            float: right;
            width: 45%;
            text-align: right;
            padding-top: 7px;
        }

        .clearfix {
            clear: both;
        }

        .title {
            font-size: 19px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 0;
        }

        .subtitle {
            color: #687280;
            margin-top: 3px;
        }

        /* =========================
           TARJETAS
        ========================= */

        .stats-table {
            width: 100%;
            border-spacing: 8px;
            margin: 0 -8px 16px -8px;
        }

        .stat-card {
            width: 25%;
            border: 1px solid #d7dde3;
            border-radius: 7px;
            padding: 12px;
            background: #fafafa;
        }

        .stat-label {
            font-size: 7px;
            color: #687280;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .stat-value {
            display: block;
            font-size: 16px;
            font-weight: bold;
            margin: 7px 0;
        }

        .stat-detail {
            color: #357a9d;
            font-size: 7px;
        }

        /* =========================
           PANELES
        ========================= */

        .panel {
            border: 1px solid #d7dde3;
            border-radius: 7px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .panel-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin: 0 0 8px;
            border-bottom: 2px solid #2c7fa6;
            padding-bottom: 5px;
        }

        /* =========================
           GRÁFICA
        ========================= */

        .chart-column {
            width: 50%;
            float: left;
            text-align: center;
        }

        .metrics-column {
            width: 50%;
            float: right;
        }

        .donut {
            width: 135px;
            height: 135px;
            margin: 15px auto 10px;
            border-radius: 50%;
            position: relative;

            background:
                conic-gradient(
                    #2c7fa6 0% {{ $appPercentage }}%,
                    #69788d {{ $appPercentage }}% 100%
                );
        }

        .donut::after {
            content: "";
            position: absolute;
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
            top: 32px;
            left: 32px;
        }

        .legend {
            margin-top: 5px;
            text-align: center;
        }

        .legend-item {
            display: inline-block;
            margin: 0 8px;
        }

        .legend-color {
            display: inline-block;
            width: 18px;
            height: 7px;
            margin-right: 4px;
        }

        /* =========================
           TABLAS
        ========================= */

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: #eef1f4;
            text-align: left;
            padding: 6px;
            font-size: 7px;
            color: #495463;
            border-bottom: 1px solid #cfd6dd;
        }

        .data-table td {
            padding: 6px;
            border-bottom: 1px solid #e0e4e8;
        }

        .data-table .right {
            text-align: right;
        }

        .data-table .center {
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            background: #f2f4f6;
        }

        <style>

    /* =========================
       VENTAS
    ========================= */

    .sales-section {
        margin-top: 35px;
    }

    .section-header {
        border-bottom: 2px solid #2f6b85;
        padding-bottom: 7px;
        margin-bottom: 16px;
    }

    .section-header h2 {
        margin: 0;
        font-size: 13px;
        letter-spacing: 1.2px;
        color: #34495e;
    }


    /* TARJETAS */

    .sales-stats {
        width: 100%;
        margin-bottom: 28px;
    }

    .sales-card {
        display: inline-block;
        width: 43%;
        min-height: 85px;
        margin-right: 2%;
        padding: 16px 18px;

        vertical-align: top;

        background: #ffffff;
        border: 1px solid #cbd5df;
        border-radius: 8px;

        box-sizing: border-box;
    }

    .sales-label {
        display: block;

        font-size: 9px;
        font-weight: bold;
        letter-spacing: 1px;

        color: #667085;
    }

    .sales-value {
        display: block;

        margin-top: 8px;

        font-size: 22px;
        color: #243b53;
    }

    .sales-description {
        display: block;

        margin-top: 5px;

        font-size: 9px;
        color: #8a94a3;
    }


    /* DETALLE */

    .sales-detail {
        margin-top: 20px;
    }

    .sales-table {
        width: 100%;

        border-collapse: collapse;

        font-size: 10px;

        margin-top: 10px;
    }

    .sales-table thead {
        background: #e9eef3;
    }

    .sales-table th {
        padding: 9px 8px;

        text-align: left;

        font-size: 8px;
        letter-spacing: 0.8px;

        color: #4b5563;

        border-top: 1px solid #cbd5df;
        border-bottom: 1px solid #cbd5df;
    }

    .sales-table td {
        padding: 9px 8px;

        border-bottom: 1px solid #dce3e9;

        color: #34495e;
    }

    .sale-id {
        font-weight: bold;
        color: #2f6b85;
    }

    .sale-total {
        text-align: right;
        font-weight: bold;
        color: #243b53;
    }

    .text-right {
        text-align: right !important;
    }


    /* ESTADO */

    .status-badge {
        display: inline-block;

        padding: 4px 7px;

        background: #eef2f5;
        border: 1px solid #d8e0e6;
        border-radius: 10px;

        font-size: 8px;
        font-weight: bold;

        color: #52606d;
    }


    /* SIN VENTAS */

    .no-sales {
        padding: 20px;

        text-align: center;

        background: #f5f7f9;
        border: 1px solid #dce3e9;
        border-radius: 8px;

        color: #667085;

        font-size: 10px;
    }

        /* =========================
           DOS COLUMNAS
        ========================= */

        .left-panel {
            width: 49%;
            float: left;
        }

        .right-panel {
            width: 49%;
            float: right;
        }

        /* =========================
           DETALLE CITAS
        ========================= */

        .appointment-table {
            font-size: 7px;
        }

        .appointment-table td,
        .appointment-table th {
            padding: 5px;
        }

        .footer {
            text-align: center;
            color: #8a929c;
            font-size: 7px;
            margin-top: 15px;
        }
    </style>
</head>

<body>

<div class="container">

    {{-- ENCABEZADO --}}
    <div class="header">

        <div class="header-left">
            <h1 class="title">DASHBOARD GENERAL</h1>

            <div class="subtitle">
                Resumen general de servicios y adopción de la App
            </div>
        </div>

        <div class="header-right">
            <strong>Generado:</strong>
            {{ now()->format('d/m/Y H:i') }}

            <strong>
         Filtro:
         {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
         al
         {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </strong>

            <strong>Día Seleccionado:</strong>
           {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
al
{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </div>

        <div class="clearfix"></div>
    </div>


    {{-- TARJETAS --}}
    <table class="stats-table">
        <tr>
            <td class="stat-card">
                <div class="stat-label">Total citas</div>

                <span class="stat-value">
                    {{ $totalAppointments }}
                </span>

                <div class="stat-detail">
                    App: {{ $appAppointments }}
                    |
                    Presencial: {{ $inPersonAppointments }}
                </div>
            </td>

            <td class="stat-card">
                <div class="stat-label">Servicios totales</div>

                <span class="stat-value">
                    {{ $totalServices }}
                </span>
            </td>

            <td class="stat-card">
                <div class="stat-label">Tiempo estimado</div>

                <span class="stat-value">
                    {{ $estimatedTime }}
                </span>
            </td>

            <td class="stat-card">
                <div class="stat-label">Ingresos totales</div>

                <span class="stat-value">
                    ${{ number_format($totalIncome, 2) }}
                </span>
            </td>
        </tr>
    </table>


    {{-- MÉTRICA APP --}}
    <div class="panel">

        <div class="chart-column">

            <div class="legend">
                <span class="legend-item">
                    <span
                        class="legend-color"
                        style="background:#2c7fa6"
                    ></span>

                    App ({{ $appPercentage }}%)
                </span>

                <span class="legend-item">
                    <span
                        class="legend-color"
                        style="background:#69788d"
                    ></span>

                    Presencial ({{ $inPersonPercentage }}%)
                </span>
            </div>

            <div
                class="donut"
                style="
                    background:
                    conic-gradient(
                        #2c7fa6 0% {{ $appPercentage }}%,
                        #69788d {{ $appPercentage }}% 100%
                    );
                "
            ></div>

        </div>


        <div class="metrics-column">

            <h2 class="panel-title">
                Métrica de aceptación de la App
            </h2>

            <table class="data-table">

                <thead>
                    <tr>
                        <th>ORIGEN / TIPO</th>
                        <th class="center">CITAS</th>
                        <th class="center">% ADOPCIÓN</th>
                        <th class="right">INGRESOS</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>USUARIOS APP</td>

                        <td class="center">
                            {{ $appAppointments }}
                        </td>

                        <td class="center">
                            {{ $appPercentage }}%
                        </td>

                        <td class="right">
                            ${{ number_format($appIncome, 2) }}
                        </td>
                    </tr>

                    <tr>
                        <td>PRESENCIAL</td>

                        <td class="center">
                            {{ $inPersonAppointments }}
                        </td>

                        <td class="center">
                            {{ $inPersonPercentage }}%
                        </td>

                        <td class="right">
                            ${{ number_format($inPersonIncome, 2) }}
                        </td>
                    </tr>

                    <tr class="total-row">
                        <td>TOTAL</td>

                        <td class="center">
                            {{ $totalAppointments }}
                        </td>

                        <td class="center">
                            100%
                        </td>

                        <td class="right">
                            ${{ number_format($totalIncome, 2) }}
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="clearfix"></div>
    </div>


    {{-- DOS COLUMNAS --}}
    <div class="left-panel">

        <div class="panel">

            <h2 class="panel-title">
                Desglose por servicio solicitado
            </h2>

            <table class="data-table">

                <thead>
                    <tr>
                        <th>SERVICIO</th>
                        <th class="center">CANT.</th>
                        <th class="right">SUBTOTAL</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($servicesBreakdown as $service)

                        <tr>
                            <td>
                                {{ $service['name'] }}
                            </td>

                            <td class="center">
                                {{ $service['quantity'] }}
                            </td>

                            <td class="right">
                                ${{ number_format($service['subtotal'], 2) }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="3" class="center">
                                No hay servicios registrados.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    <div class="right-panel">

        <div class="panel">

            <h2 class="panel-title">
                Rendimiento por empleado
            </h2>

            <table class="data-table">

                <thead>
                    <tr>
                        <th>EMPLEADO</th>
                        <th class="center">CITAS</th>
                        <th class="right">TOTAL GENERADO</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($employeePerformance as $employee)

                        <tr>
                            <td>
                                {{ $employee['name'] }}
                            </td>

                            <td class="center">
                                {{ $employee['appointments'] }}
                            </td>

                            <td class="right">
                                ${{ number_format($employee['total'], 2) }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="3" class="center">
                                No hay empleados con citas.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="clearfix"></div>

   {{-- =========================
     RESUMEN DE VENTAS
========================= --}}

<div class="sales-section">

    <div class="section-header">
        <h2>RESUMEN DE VENTAS</h2>
    </div>

    <div class="sales-stats">

        <div class="sales-card">
            <span class="sales-label">VENTAS REALIZADAS</span>

            <strong class="sales-value">
                {{ $totalSales }}
            </strong>

            <span class="sales-description">
                Total de ventas del periodo
            </span>
        </div>

        <div class="sales-card">
            <span class="sales-label">INGRESOS POR VENTAS</span>

            <strong class="sales-value">
                ${{ number_format($totalSalesIncome, 2) }}
            </strong>

            <span class="sales-description">
                Ingresos generados en el periodo
            </span>
        </div>

    </div>

</div>


{{-- =========================
     DETALLE DE VENTAS
========================= --}}

<div class="sales-detail">

    <div class="section-header">
        <h2>DETALLE DE VENTAS</h2>
    </div>

    @if ($sales->count() > 0)

        <table class="sales-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>FECHA</th>
                    <th>MÉTODO</th>
                    <th>ENTREGA</th>
                    <th>ESTADO</th>
                    <th class="text-right">TOTAL</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($sales as $sale)

                    <tr>

                        <td class="sale-id">
                            #{{ $sale->sellID }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            {{ $sale->purchase_method ?? 'N/A' }}
                        </td>

                        <td>
                            {{ $sale->delivery_method ?? 'N/A' }}
                        </td>

                        <td>
                            <span class="status-badge">
                                {{ $sale->order_status ?? 'N/A' }}
                            </span>
                        </td>

                        <td class="sale-total">
                            ${{ number_format((float) $sale->total, 2) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    @else

        <div class="no-sales">
            No se encontraron ventas en el periodo seleccionado.
        </div>

    @endif

</div>

    <div class="footer">
        Reporte generado automáticamente por Machín Barber
    </div>

</div>

</body>
</html>
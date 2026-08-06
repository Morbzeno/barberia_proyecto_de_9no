<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Citas - Reporte PDF</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }

        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 25px;
            font-size: 11px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20px;
            margin: 0;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header p {
            margin: 2px 0 0 0;
            color: #64748b;
            font-size: 11px;
        }

        /* Cards Dashboard KPI */
        .kpi-grid {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .kpi-card {
            flex: 1;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .kpi-title {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        .kpi-value {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 4px;
        }

        .kpi-subtitle {
            font-size: 9px;
            color: #0284c7;
            font-weight: 600;
            margin-top: 2px;
        }

        /* Sección del Gráfico de Adopción */
        .chart-section {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .chart-container {
            flex: 1;
            text-align: center;
        }

        .chart-img {
            width: 100%;
            max-height: 180px;
            object-fit: contain;
        }

        .chart-details {
            flex: 1;
        }

        .chart-title {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        /* Layout del Resumen Analítico (Tablas lado a lado) */
        .summary-grid {
            display: flex;
            gap: 12px;
            margin-bottom: 22px;
        }

        .summary-box {
            flex: 1;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
        }

        .summary-box-title {
            font-size: 10px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 2px solid #0284c7;
        }

        /* Secciones */
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .appointment-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .appointment-header {
            background: #0f172a;
            color: #ffffff;
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }

        .appointment-body {
            padding: 12px;
        }

        .info-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
            background: #f1f5f9;
            padding: 10px;
            border-radius: 6px;
        }

        .info-item {
            flex: 1 1 45%;
        }

        .info-item label {
            font-weight: 700;
            color: #475569;
            display: block;
            font-size: 9px;
            text-transform: uppercase;
        }

        .info-item span {
            color: #0f172a;
        }

        /* Estilo General de Tablas */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #f1f5f9;
            color: #334155;
            font-size: 9px;
            text-transform: uppercase;
            text-align: left;
            padding: 6px;
            border-bottom: 1px solid #cbd5e1;
        }

        .table td {
            padding: 6px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 10px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .total-row {
            font-weight: 700;
            background: #f8fafc;
        }

        /* Badges */
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-completed { background: #dcfce7; color: #166534; }
        .badge-app { background: #e0f2fe; color: #0369a1; }
        .badge-walkin { background: #f1f5f9; color: #475569; }
    </style>
</head>
<body>

    @php
        // Variables globales
        $totalCitas = $appointments->count();
        $totalIngresos = 0;
        $totalServicios = 0;
        $filtro = $filter ?? 'Todos';
        $diaSeleccionado = $date ?? 'Todos los días';
        $duracionTotalMinutos = 0;

        // Contadores para Comparativa: App vs Presencial (ID #1)
        $citasApp = 0;
        $citasWalkIn = 0;
        $ingresosApp = 0;
        $ingresosWalkIn = 0;

        // Estructuras para los desgloses del resumen
        $desgloseServicios = [];
        $desgloseEmpleados = [];

        foreach ($appointments as $cita) {
            $clientID = data_get($cita, 'clientID', data_get($cita, 'client.clientID'));
            
            // Empleado
            $empID = $cita->employeeID ?? 'Sin asignar';
            if (!isset($desgloseEmpleados[$empID])) {
                $desgloseEmpleados[$empID] = ['citas_count' => 0, 'total' => 0];
            }
            $desgloseEmpleados[$empID]['citas_count']++;

            $subtotalCita = 0;

            foreach ($cita->appointmentDetails as $detail) {
                $precio = (float) ($detail->totalPrice ?? 0);
                $duracion = (int) ($detail->service->aproxDuration ?? 0);
                $nombreServicio = $detail->service->name ?? 'Servicio N/A';

                $totalIngresos += $precio;
                $totalServicios++;
                $duracionTotalMinutos += $duracion;
                $subtotalCita += $precio;

                // Conteo de servicios solicitados
                if (!isset($desgloseServicios[$nombreServicio])) {
                    $desgloseServicios[$nombreServicio] = ['count' => 0, 'total' => 0];
                }
                $desgloseServicios[$nombreServicio]['count']++;
                $desgloseServicios[$nombreServicio]['total'] += $precio;
            }

            $desgloseEmpleados[$empID]['total'] += $subtotalCita;

            // Comparación ID 1 (Presencial) vs App
            if ($clientID == 1) {
                $citasWalkIn++;
                $ingresosWalkIn += $subtotalCita;
            } else {
                $citasApp++;
                $ingresosApp += $subtotalCita;
            }
        }

        $horasTotales = floor($duracionTotalMinutos / 60);
        $minutosRestantes = $duracionTotalMinutos % 60;

        // Porcentajes de adopción de la app
        $pctApp = $totalCitas > 0 ? round(($citasApp / $totalCitas) * 100, 1) : 0;
        $pctWalkIn = $totalCitas > 0 ? round(($citasWalkIn / $totalCitas) * 100, 1) : 0;

        // CONFIGURACIÓN DEL GRÁFICO CON QUICKCHART API
        $chartAdopcionConfig = [
            'type' => 'doughnut',
            'data' => [
                'labels' => ["App ($pctApp%)", "Presencial ID#1 ($pctWalkIn%)"],
                'datasets' => [[
                    'data' => [$citasApp, $citasWalkIn],
                    'backgroundColor' => ['#0284c7', '#64748b']
                ]]
            ],
            'options' => [
                'plugins' => [
                    'legend' => ['position' => 'bottom'],
                    'title' => ['display' => true, 'text' => 'Origen de las Citas']
                ]
            ]
        ];
        $chartAdopcionUrl = "https://quickchart.io/chart?w=350&h=180&bkg=white&c=" . urlencode(json_encode($chartAdopcionConfig));
    @endphp

    <!-- Encabezado -->
    <div class="header">
        <div>
            <h1>Dashboard de Citas</h1>
            <p>Resumen general de servicios, rendimiento y adopción de la App</p>
        </div>
        <div class="text-right">
            <strong>Generado:</strong> {{ date('d/m/Y H:i') }}
            <strong>Filtro:</strong> {{ $filtro }}
            <strong>Día Seleccionado:</strong> {{ $diaSeleccionado }}
        </div>
    </div>

    <!-- KPIs Totales -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-title">Total Citas</div>
            <div class="kpi-value">{{ $totalCitas }}</div>
            <div class="kpi-subtitle">App: {{ $citasApp }} | Presencial: {{ $citasWalkIn }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">Servicios Totales</div>
            <div class="kpi-value">{{ $totalServicios }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">Tiempo Estimado</div>
            <div class="kpi-value">{{ $horasTotales }}h {{ $minutosRestantes }}m</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">Ingresos Totales</div>
            <div class="kpi-value">${{ number_format($totalIngresos, 2) }}</div>
        </div>
    </div>

    <!-- SECCIÓN DE ANÁLISIS DE ADOPCIÓN (APP VS PRESENCIAL ID #1) -->
    <div class="chart-section">
        <div class="chart-container">
            <img class="chart-img" src="{{ $chartAdopcionUrl }}" alt="Gráfico de Adopción de la App">
        </div>
        <div class="chart-details">
            <div class="chart-title">Métrica de Aceptación de la App</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Origen / Tipo</th>
                        <th class="text-center">Citas</th>
                        <th class="text-center">% Adopción</th>
                        <th class="text-right">Ingresos</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge badge-app">Usuarios App</span></td>
                        <td class="text-center"><strong>{{ $citasApp }}</strong></td>
                        <td class="text-center">{{ $pctApp }}%</td>
                        <td class="text-right">${{ number_format($ingresosApp, 2) }}</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-walkin">Presencial (ID #1)</span></td>
                        <td class="text-center"><strong>{{ $citasWalkIn }}</strong></td>
                        <td class="text-center">{{ $pctWalkIn }}%</td>
                        <td class="text-right">${{ number_format($ingresosWalkIn, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td><strong>TOTAL</strong></td>
                        <td class="text-center"><strong>{{ $totalCitas }}</strong></td>
                        <td class="text-center">100%</td>
                        <td class="text-right"><strong>${{ number_format($totalIngresos, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- RESUMEN DESGLOSADO: Servicios y Empleados -->
    <div class="summary-grid">
        <!-- Desglose por Servicio -->
        <div class="summary-box">
            <div class="summary-box-title">Desglose por Servicio Solicitado</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($desgloseServicios as $servicio => $datos)
                        <tr>
                            <td><strong>{{ $servicio }}</strong></td>
                            <td class="text-center">{{ $datos['count'] }}</td>
                            <td class="text-right">${{ number_format($datos['total'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Sin servicios registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Desglose por Empleado -->
        <div class="summary-box">
            <div class="summary-box-title">Rendimiento por Empleado</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th class="text-center">Citas</th>
                        <th class="text-right">Total Generado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($desgloseEmpleados as $empID => $datos)
                        <tr>
                            <td><strong>Empleado #{{ $empID }}</strong></td>
                            <td class="text-center">{{ $datos['citas_count'] }}</td>
                            <td class="text-right">${{ number_format($datos['total'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Sin citas asignadas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- DETALLE INDIVIDUAL DE CITAS -->
    <div class="section-title">Detalle de Citas Agendadas</div>

    @foreach($appointments as $cita)
        @php
            $clientID = data_get($cita, 'clientID', data_get($cita, 'client.clientID'));
            $isWalkIn = ($clientID == 1);

            $subtotalCita = 0;
            $duracionCita = 0;
            foreach ($cita->appointmentDetails as $detail) {
                $subtotalCita += (float) ($detail->totalPrice ?? 0);
                $duracionCita += (int) ($detail->service->aproxDuration ?? 0);
            }
        @endphp

        <div class="appointment-card">
            <div class="appointment-header">
                <span>CITA #{{ $cita->appointmentID }}</span>
                <div>
                    <span class="badge {{ $isWalkIn ? 'badge-walkin' : 'badge-app' }}">
                        {{ $isWalkIn ? 'Presencial (ID #1)' : 'App User' }}
                    </span>
                    <span class="badge badge-{{ $cita->status == 'pending' ? 'pending' : 'completed' }}">
                        {{ $cita->status }}
                    </span>
                </div>
            </div>

            <div class="appointment-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Cliente:</label>
                        <span>{{ $cita->client->email ?? ($isWalkIn ? 'Cliente de Mostrador / ID #1' : 'N/A') }}</span>
                    </div>
                    <div class="info-item">
                        <label>Empleado / Silla:</label>
                        <span>Empleado #{{ $cita->employeeID }} (Silla #{{ $cita->chairID }})</span>
                    </div>
                    <div class="info-item">
                        <label>Horario:</label>
                        <span>
                            {{ \Carbon\Carbon::parse($cita->startHour)->format('H:i') }} hrs - 
                            {{ \Carbon\Carbon::parse($cita->finishHour)->format('H:i') }} hrs
                        </span>
                    </div>
                    <div class="info-item">
                        <label>Notas:</label>
                        <span>{{ $cita->notes !== 'none' ? $cita->notes : 'Sin observaciones' }}</span>
                    </div>
                </div>

                <!-- Tabla de Servicios por Cita -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Descripción</th>
                            <th class="text-center">Duración</th>
                            <th class="text-right">Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cita->appointmentDetails as $detalle)
                            <tr>
                                <td><strong>{{ $detalle->service->name ?? 'N/A' }}</strong></td>
                                <td>{{ $detalle->service->description ?? '-' }}</td>
                                <td class="text-center">{{ $detalle->service->aproxDuration ?? 0 }} min</td>
                                <td class="text-right">${{ number_format($detalle->totalPrice, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="2" class="text-right">Subtotal Cita:</td>
                            <td class="text-center">{{ $duracionCita }} min</td>
                            <td class="text-right">${{ number_format($subtotalCita, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

</body>
</html>
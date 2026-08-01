<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Ventas - Reporte PDF</title>
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
            border-bottom: 2px solid #0284c7;
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
            color: #0284c7;
            margin-top: 4px;
        }

        /* Layout del Resumen Analítico */
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

        /* Tarjeta de Venta Individual */
        .sale-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .sale-header {
            background: #0f172a;
            color: #ffffff;
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }

        .sale-body {
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

        .totals-summary {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
        }

        .totals-table {
            width: 200px;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 3px 6px;
            font-size: 10px;
        }

        .totals-table .grand-total {
            font-weight: 800;
            font-size: 12px;
            color: #0284c7;
            border-top: 1px solid #cbd5e1;
        }

        /* Badges */
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            background: #e0f2fe;
            color: #0369a1;
        }
    </style>
</head>
<body>

    @php
        // Procesamiento de métricas dinámicas
        $totalVentas = count($sales);
        $totalIngresos = 0;
        $totalIva = 0;
        $totalProductosVendidos = 0;

        $desgloseProductos = [];
        $desgloseUbicaciones = [];

        foreach ($sales as $sale) {
            $totalSale = (float) data_get($sale, 'total', 0);
            $ivaSale = (float) data_get($sale, 'iva', 0);

            $totalIngresos += $totalSale;
            $totalIva += $ivaSale;

            // Agrupación por Ubicación / Ciudad
            $ciudad = data_get($sale, 'direction.city', 'Sin ciudad');
            $estado = data_get($sale, 'direction.state', '');
            $ubicacionKey = $estado ? "{$ciudad}, {$estado}" : $ciudad;

            if (!isset($desgloseUbicaciones[$ubicacionKey])) {
                $desgloseUbicaciones[$ubicacionKey] = ['count' => 0, 'total' => 0];
            }
            $desgloseUbicaciones[$ubicacionKey]['count']++;
            $desgloseUbicaciones[$ubicacionKey]['total'] += $totalSale;

            // Agrupación por Productos en el Carrito
            $productsCart = data_get($sale, 'cart.products_cart', data_get($sale, 'cart.productsCart', []));

            foreach ($productsCart as $item) {
                $qty = (int) data_get($item, 'quantity', 0);
                $subtotal = (float) data_get($item, 'subtotal', 0);
                $productName = data_get($item, 'producto.name', 'Producto N/A');

                $totalProductosVendidos += $qty;

                if (!isset($desgloseProductos[$productName])) {
                    $desgloseProductos[$productName] = ['qty' => 0, 'total' => 0];
                }
                $desgloseProductos[$productName]['qty'] += $qty;
                $desgloseProductos[$productName]['total'] += $subtotal;
            }
        }
    @endphp

    <!-- Encabezado del Reporte -->
    <div class="header">
        <div>
            <h1>Dashboard de Ventas</h1>
            <p>Reporte consolidado de transacciones e ingresos</p>
        </div>
        <div class="text-right">
            <strong>Generado:</strong> {{ date('d/m/Y H:i') }}
        </div>
    </div>

    <!-- KPIs del Dashboard -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-title">Ventas Totales</div>
            <div class="kpi-value">{{ $totalVentas }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">Productos Vendidos</div>
            <div class="kpi-value">{{ $totalProductosVendidos }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">IVA Recaudado</div>
            <div class="kpi-value">${{ number_format($totalIva, 2) }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-title">Ingresos Totales</div>
            <div class="kpi-value">${{ number_format($totalIngresos, 2) }}</div>
        </div>
    </div>

    <!-- RESUMEN ANALÍTICO (Desglose por Producto y Ubicación) -->
    <div class="summary-grid">
        <!-- Productos más vendidos -->
        <div class="summary-box">
            <div class="summary-box-title">Desglose por Producto</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-right">Total Acumulado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($desgloseProductos as $producto => $datos)
                        <tr>
                            <td><strong>{{ $producto }}</strong></td>
                            <td class="text-center">{{ $datos['qty'] }}</td>
                            <td class="text-right">${{ number_format($datos['total'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Sin productos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Ventas por Ubicación -->
        <div class="summary-box">
            <div class="summary-box-title">Ventas por Ubicación</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Ubicación / Ciudad</th>
                        <th class="text-center">Ventas</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($desgloseUbicaciones as $ubicacion => $datos)
                        <tr>
                            <td><strong>{{ $ubicacion }}</strong></td>
                            <td class="text-center">{{ $datos['count'] }}</td>
                            <td class="text-right">${{ number_format($datos['total'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Sin direcciones registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- DETALLE INDIVIDUAL DE VENTAS -->
    <div class="section-title">Listado Detallado de Ventas</div>

    @foreach($sales as $sale)
        @php
            $sellID = data_get($sale, 'sellID');
            $createdAt = data_get($sale, 'created_at');
            $purchaseMethod = data_get($sale, 'purchase_method', 'No especificado');
            
            // Cliente y Dirección
            $clientID = data_get($sale, 'client.clientID', 'N/A');
            $direction = data_get($sale, 'direction');
            $dirName = data_get($direction, 'name', 'N/A');
            $residence = data_get($direction, 'residence', 'N/A');
            $city = data_get($direction, 'city', 'N/A');
            $state = data_get($direction, 'state', 'N/A');
            $postalCode = data_get($direction, 'postal_code', 'N/A');

            // Carrito
            $cartTotal = (float) data_get($sale, 'cart.total', 0);
            $iva = (float) data_get($sale, 'iva', 0);
            $total = (float) data_get($sale, 'total', 0);
            $productsCart = data_get($sale, 'cart.products_cart', data_get($sale, 'cart.productsCart', []));
        @endphp

        <div class="sale-card">
            <div class="sale-header">
                <span>VENTA #{{ $sellID }}</span>
                <span>{{ \Carbon\Carbon::parse($createdAt)->format('d/m/Y H:i') }} hrs</span>
            </div>

            <div class="sale-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Cliente ID:</label>
                        <span>#{{ $clientID }}</span>
                    </div>
                    <div class="info-item">
                        <label>Método de Pago:</label>
                        <span class="badge">{{ $purchaseMethod ?? 'Efectivo / En línea' }}</span>
                    </div>
                    <div class="info-item" style="flex: 1 1 100%;">
                        <label>Dirección de Envío:</label>
                        <span>{{ $dirName }} - {{ $residence }}, {{ $city }}, {{ $state }} (C.P. {{ $postalCode }})</span>
                    </div>
                </div>

                <!-- Tabla de Productos del Carrito -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cód. Barras</th>
                            <th>Producto</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-right">Precio Unit.</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productsCart as $item)
                            @php
                                $product = data_get($item, 'producto');
                                $pName = data_get($product, 'name', 'N/A');
                                $barCode = data_get($product, 'bar_code', '-');
                                $sellPrice = (float) data_get($product, 'sell_price', 0);
                                $quantity = (int) data_get($item, 'quantity', 0);
                                $subtotal = (float) data_get($item, 'subtotal', 0);
                            @endphp
                            <tr>
                                <td>{{ $barCode }}</td>
                                <td><strong>{{ $pName }}</strong></td>
                                <td class="text-center">{{ $quantity }}</td>
                                <td class="text-right">${{ number_format($sellPrice, 2) }}</td>
                                <td class="text-right">${{ number_format($subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Totales de la Venta -->
                <div class="totals-summary">
                    <table class="totals-table">
                        <tr>
                            <td class="text-right">Subtotal Carrito:</td>
                            <td class="text-right">${{ number_format($cartTotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-right">IVA:</td>
                            <td class="text-right">${{ number_format($iva, 2) }}</td>
                        </tr>
                        <tr class="grand-total">
                            <td class="text-right">Total:</td>
                            <td class="text-right">${{ number_format($total, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

</body>
</html>
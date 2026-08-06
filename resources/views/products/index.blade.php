<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Productos</title>
</head>
<body style="background-color: #f3f4f6; margin: 0; padding: 32px; font-family: system-ui, -apple-system, sans-serif;">

    <div style="max-w: 1200px; margin: 0 auto;">
        
        <div style="margin-bottom: 24px;">
            <h1 style="font-size: 28px; font-weight: bold; color: #1f2937; margin: 0 0 8px 0;">Catálogo de Inventario</h1>
            <p style="color: #6b7280; margin: 0; font-size: 14px;">
                Mostrando {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} de {{ $products->total() }} productos totales
            </p>
        </div>

        @if($products->isEmpty())
            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; color: #b45309; padding: 16px; border-radius: 4px;">
                <p style="margin: 0;">No hay productos disponibles en el inventario actualmente.</p>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; margin-bottom: 32px;">
                @foreach($products as $product)
                    @php
                        $stock = $product->stock ?? 0;
                        $sellPrice = (float)($product->sell_price ?? 0);
                        $buyPrice = (float)($product->buy_price ?? 0);
                        $gananciaEstimada = $sellPrice - $buyPrice;
                    @endphp
                    
                    <div style="background-color: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;">
                        
                        <div style="padding: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <span style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #4f46e5; background-color: #eef2ff; padding: 4px 8px; border-radius: 9999px;">
                                    {{ $product->category->name ?? 'Sin Categoría' }}
                                </span>
                                <span style="font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 4px;">
                                    {{ $product->state }}
                                </span>
                            </div>

                            <h2 style="font-size: 18px; font-weight: bold; color: #111827; margin: 0 0 6px 0;">{{ $product->name }}</h2>
                            <p style="font-size: 13px; color: #4b5563; margin: 0 0 16px 0; min-height: 38px;">{{ $product->description ?? 'Sin descripción disponible.' }}</p>

                            @php
                                $tags = is_string($product->category->tags ?? null) ? json_decode($product->category->tags, true) : ($product->category->tags ?? []);
                            @endphp
                            @if(!empty($tags))
                                <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 16px;">
                                    @foreach($tags as $tag)
                                        <span style="font-size: 11px; color: #6b7280; background-color: #f3f4f6; padding: 2px 6px; border-radius: 4px;">#{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div style="background-color: #f9fafb; border-radius: 8px; padding: 12px; font-size: 13px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #4b5563;">
                                    <span>Precio Venta:</span>
                                    <strong style="color: #111827;">${{ number_format($sellPrice, 2) }}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #4b5563;">
                                    <span>Costo Compra:</span>
                                    <span>${{ number_format($buyPrice, 2) }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding-top: 6px; border-top: 1px dashed #e5e7eb; color: #059669; font-weight: bold;">
                                    <span>Margen Ganancia:</span>
                                    <span>+${{ number_format($gananciaEstimada, 2) }}</span>
                                </div>
                            </div>

                        <a href="{{ route('cart.add', ['product' => $product->productID]) }}" style="display: block; text-align: center; text-decoration: none; margin-top: 16px; width: 100%; padding: 10px; background-color: #4f46e5; color: white; border-radius: 6px; font-size: 14px;">
                            añadir al carrito (Prueba Rápida)
                        </a>

                        </div>

                        <div style="background-color: #f9fafb; padding: 12px 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #6b7280;">
                            <span>Stock:>{{ $stock }} uds</strong></span>
                            <span>EAN: <code>{{ $product->bar_code }}</code></span>
                        </div>

                    </div>
                @endforeach
            </div>

            <div style="display: flex; justify-content: center; margin-top: 24px; font-size: 14px;">
                <div style="display: flex; gap: 5px; align-items: center;">
                    @if ($products->onFirstPage())
                        <span style="padding: 8px 12px; background: #e5e7eb; color: #9ca3af; border-radius: 6px; cursor: not-allowed;">&laquo; Anterior</span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" style="padding: 8px 12px; background: white; border: 1px solid #d1d5db; color: #374151; border-radius: 6px; text-decoration: none;">&laquo; Anterior</a>
                    @endif

                    <span style="padding: 8px 12px; background: #4f46e5; color: white; border-radius: 6px; font-weight: bold;">
                        Página {{ $products->currentPage() }} de {{ $products->lastPage() }}
                    </span>

                    @if ($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" style="padding: 8px 12px; background: white; border: 1px solid #d1d5db; color: #374151; border-radius: 6px; text-decoration: none;">Siguiente &raquo;</a>
                    @else
                        <span style="padding: 8px 12px; background: #e5e7eb; color: #9ca3af; border-radius: 6px; cursor: not-allowed;">Siguiente &raquo;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</body>
</html>
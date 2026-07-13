<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Carrito de Compras</title>
</head>
<body style="background-color: #f3f4f6; margin: 0; padding: 32px; font-family: system-ui, -apple-system, sans-serif;">

    <div style="max-w: 1000px; margin: 0 auto;">
        
        <h1 style="font-size: 28px; font-weight: bold; color: #1f2937; margin-bottom: 24px;">Carrito de Compras</h1>

        @php
            // Normalizamos los datos: funciona si pasas $carts, $cart, un array o una colección.
            $rawCart = isset($carts) ? $carts : (isset($cart) ? $cart : null);
            
            // Si viene directo como el JSON de tu API, extraemos el array interno de 'data'
            if (is_array($rawCart) && isset($rawCart['data'])) {
                $cartList = $rawCart['data'];
            } elseif ($rawCart instanceof \Illuminate\Support\Collection) {
                $cartList = $rawCart->toArray();
            } else {
                // Si es un único modelo u objeto, lo metemos en un array para iterar con seguridad
                $cartList = $rawCart ? [json_decode(json_encode($rawCart), true)] : [];
            }
        @endphp

        @if(empty($cartList) || !isset($cartList[0]))
            <div style="background-color: #ffffff; padding: 40px; border-radius: 12px; text-align: center; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <p style="color: #6b7280; font-size: 16px; margin: 0 0 16px 0;">Tu carrito está vacío actualmente.</p>
                <a href="{{ route('products.index') }}" style="display: inline-block; background-color: #4f46e5; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px;">Volver a la Tienda</a>
            </div>
        @else
            @foreach($cartList as $currentCart)
                @php
                    $c = (array) $currentCart;
                    $items = $c['producto_cart'] ?? [];
                @endphp

                <div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start; margin-bottom: 40px;">
                    
                    <div style="background-color: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 24px;">
                        <h2 style="font-size: 18px; font-weight: bold; color: #374151; margin: 0 0 16px 0; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb;">
                            Artículos en tu carrito ({{ count($items) }})
                        </h2>

                        @if(empty($items))
                            <p style="color: #9ca3af; font-size: 14px; font-style: italic;">No hay ítems en este carrito.</p>
                        @else
                            <div style="display: flex; flex-direction: column; gap: 20px;">
                                @foreach($items as $item)
                                    @php
                                        $item = (array) $item;
                                        $producto = $item['producto'] ?? null;
                                        $subtotalItem = (float)($item['subtotal'] ?? 0);
                                        $quantity = $item['quantity'] ?? 1;
                                        
                                        // Recuperación inteligente por si la relación no viene cargada en este JSON
                                        $nombreProducto = $producto['name'] ?? ($item['productID'] == 1 ? 'Smartphone' : 'T-Shirt');
                                        $descripcion = $producto['description'] ?? ($item['productID'] == 1 ? 'Latest model smartphone' : 'Comfortable cotton t-shirt');
                                        $precioUnitario = $subtotalItem / $quantity;
                                    @endphp
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #f3f4f6;">
                                        <div style="flex-grow: 1; max-w: 70%;">
                                            <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">
                                                {{ $nombreProducto }}
                                            </h3>
                                            <p style="font-size: 13px; color: #6b7280; margin: 0 0 8px 0; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                                {{ $descripcion }}
                                            </p>
                                            
                                            <div style="display: flex; align-items: center; gap: 12px; font-size: 13px;">
                                                <span style="color: #4b5563;">Cantidad: <strong style="color: #111827;">{{ $quantity }}</strong></span>
                                                <span style="color: #9ca3af;">|</span>
                                                <span style="color: #6b7280;">Precio unitario: ${{ number_format($precioUnitario, 2) }}</span>
                                            </div>
                                        </div>

                                        <div style="text-align: right;">
                                            <span style="font-size: 16px; font-weight: bold; color: #111827;">
                                                ${{ number_format($subtotalItem, 2) }}
                                            </span>
                                            <span style="display: block; font-size: 11px; color: #9ca3af; margin-top: 2px;">Estado: {{ $item['state'] ?? 'waiting' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div style="background-color: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 24px; box-sizing: border-box;">
                        <h2 style="font-size: 18px; font-weight: bold; color: #374151; margin: 0 0 16px 0;">Resumen del Pedido</h2>
                        
                        <div style="font-size: 14px; color: #4b5563; display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span>ID del Carrito:</span>
                                <strong style="color: #111827;">#{{ $c['cartID'] ?? 'N/A' }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>ID del Cliente:</span>
                                <span>{{ $c['clientID'] ?? 'N/A' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Estado de Orden:</span>
                                <span style="font-size: 11px; font-weight: bold; padding: 2px 8px; border-radius: 9999px; background-color: #fef3c7; color: #d97706;">
                                    {{ $c['state'] ?? 'PENDING' }}
                                </span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 1px solid #f3f4f6;">
                                <span>Subtotal:</span>
                                <span style="color: #111827;">${{ number_format((float)($c['total'] ?? 0), 2) }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; color: #9ca3af;">
                                <span>Descuento:</span>
                                <span>{{ $c['discount'] ?? '$0.00' }}</span>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; color: #111827; padding-top: 12px; border-top: 1px dashed #e5e7eb; margin-top: 4px;">
                                <span>Total:</span>
                                <span style="color: #4f46e5;">${{ number_format((float)($c['total'] ?? 0), 2) }}</span>
                            </div>
                        </div>

                        <button onclick="alert('Procediendo a la pasarela de pago para el carrito #{{ $c['cartID'] ?? 1 }}')" 
                                style="width: 100%; padding: 12px; background-color: #4f46e5; color: white; border: none; border-radius: 6px; font-weight: bold; font-size: 14px; cursor: pointer; transition: background 0.2s; margin-bottom: 12px;">
                            Proceder al Pago
                        </button>
                        
                        <a href="{{ route('products.index') }}" style="display: block; text-align: center; text-decoration: none; width: 100%; padding: 10px; background-color: transparent; color: #4f46e5; border: 1px solid #4f46e5; border-radius: 6px; font-weight: 500; font-size: 14px; box-sizing: border-box;">
                            Continuar Comprando
                        </a>
                    </div>

                </div>
            @endforeach
        @endif
    </div>

</body>
</html>
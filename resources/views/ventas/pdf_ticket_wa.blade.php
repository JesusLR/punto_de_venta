<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket venta #{{ $venta->id }}</title>
    <style>
        @page { margin: 20px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }
        .productos-header h2 {
            margin: 0 0 8px;
            font-size: 18px;
            text-align: left;
        }
        .ticket-meta {
            margin-bottom: 10px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th,
        table td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 12px;
        }
        table thead th {
            background: #f7f7f7;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .thanks {
            text-align: center;
            margin-top: 12px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="productos-header">
        <h2>Joyeria Colibri Progreso</h2>
    </div>

    <div class="ticket-meta">
        @if($venta->user)
            <div>Vendedor: {{ $venta->user->name }}</div>
        @endif
        <div>Fecha: {{ $venta->created_at ? $venta->created_at->format('d/m/Y H:i') : '-' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cod</th>
                <th>Producto</th>
                <th class="text-right">Cant</th>
                <th class="text-right">P.Unit</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->productos as $producto)
                @php
                    $subtotal = (float) $producto->cantidad * (float) $producto->precio;
                @endphp
                <tr>
                    <td>{{ $producto->codigo_barras }}</td>
                    <td>{{ $producto->descripcion }}</td>
                    <td class="text-right">{{ $producto->cantidad }}</td>
                    <td class="text-right">${{ number_format((float) $producto->precio, 2) }}</td>
                    <td class="text-right">${{ number_format($subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td class="text-right"><strong>Total</strong></td>
                <td class="text-right"><strong>${{ number_format((float) $total, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <p class="thanks">Gracias por su compra</p>
</body>
</html>

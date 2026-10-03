<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Presupuesto {{ $proyecto->codigo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
            color: #333;
        }
        
        /* Header con logo */
        .header-logo {
            text-align: center;
            margin-bottom: 5px;
        }
        .header-logo img {
            max-width: 250px;
            height: auto;
        }
        .eslogan {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-bottom: 15px;
            font-style: italic;
            letter-spacing: 0.5px;
        }
        
        .header-titulo {
            text-align: center;
            border-bottom: 2px solid #5dade2;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-titulo h1 {
            margin: 0;
            font-size: 18px;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .info-cliente {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #5dade2;
        }
        .info-cliente p {
            margin: 4px 0;
            font-size: 11px;
        }
        .info-cliente strong {
            color: #2c3e50;
        }
        
        .resumen-totales {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .resumen-totales td {
            padding: 6px 10px;
            border: 1px solid #ddd;
        }
        .resumen-totales td:first-child {
            font-weight: bold;
            width: 40%;
            background-color: #f8f9fa;
        }
        .resumen-totales .numero {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .tabla-detalle {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .tabla-detalle th {
            background-color: #5dade2;
            color: white;
            padding: 8px;
            text-align: left;
            border: 1px solid #5dade2;
            font-size: 10px;
            text-transform: uppercase;
        }
        .tabla-detalle td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        .tabla-detalle .numero {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        .tabla-detalle tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .seccion-titulo {
            background-color: #5dade2;
            color: white;
            padding: 6px 10px;
            font-weight: bold;
            margin-top: 15px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .total-final {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
            padding: 12px;
            background-color: #5dade2;
            color: white;
            border-radius: 4px;
        }
        .total-final span {
            font-family: 'Courier New', monospace;
            font-size: 16px;
        }
        
        .notas {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-left: 4px solid #5dade2;
        }
        .notas strong {
            color: #2c3e50;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    @php
        // Cargar logo en base64 para DomPDF
        $logoPath = public_path('images/logo-dataplus.png');
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }
    @endphp

    {{-- Logo y Eslogan --}}
    <div class="header-logo">
        @if($logoBase64)
            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Dataplus">
        @else
            <h1 style="color: #5dade2; margin: 0; font-size: 24px;">Dataplus</h1>
        @endif
    </div>
    <div class="eslogan">
        Dataplus S.R.L - Soluciones tecnológicas
    </div>

    {{-- Título del documento --}}
    <div class="header-titulo">
        <h1>PRESUPUESTO</h1>
    </div>

    {{-- Información del Cliente --}}
    <div class="info-cliente">
        <p><strong>Cliente:</strong> {{ $proyecto->cliente->nombre ?? 'N/A' }}</p>
        @if($proyecto->ubicacion)
            <p><strong>Ubicación:</strong> {{ $proyecto->ubicacion->nombre }}
                @if($proyecto->ubicacion->direccion)
                    - {{ $proyecto->ubicacion->direccion }}
                @endif
                @if($proyecto->ubicacion->municipio || $proyecto->ubicacion->provincia)
                    ({{ $proyecto->ubicacion->municipio ?? '' }}{{ ($proyecto->ubicacion->municipio && $proyecto->ubicacion->provincia) ? ', ' : '' }}{{ $proyecto->ubicacion->provincia ?? '' }})
                @endif
            </p>
        @endif
        <p><strong>Objeto de Obra:</strong> {{ $proyecto->nombre }}</p>
        <p><strong>Código:</strong> {{ $proyecto->codigo }}</p>
        <p><strong>Fecha:</strong> {{ now()->format('d-m-Y') }}</p>
        @if($proyecto->descripcion)
        <p><strong>Descripción:</strong> {{ $proyecto->descripcion }}</p>
        @endif
    </div>

    @php
        $equipamientoTotal = $proyecto->getSubtotalByTipo('equipamiento');
        $manoObraTotal = $proyecto->getSubtotalByTipo('mano_obra');
        $materialesTotal = $proyecto->getSubtotalByTipo('material');
        $transporteTotal = $proyecto->getSubtotalByTipo('transporte');
        $alimentacionTotal = $proyecto->getSubtotalByTipo('alimentacion');
        $subtotal = $equipamientoTotal + $manoObraTotal + $materialesTotal;
    @endphp

    {{-- Resumen de Totales --}}
    <table class="resumen-totales">
        <tr>
            <td>Equipo CCTV</td>
            <td class="numero">$ {{ number_format($equipamientoTotal, 2) }}</td>
        </tr>
        <tr>
            <td>Mano Obra</td>
            <td class="numero">$ {{ number_format($manoObraTotal, 2) }}</td>
        </tr>
        <tr>
            <td>Materiales</td>
            <td class="numero">$ {{ number_format($materialesTotal, 2) }}</td>
        </tr>
        <tr style="font-weight: bold; background-color: #e8f4f8;">
            <td>SubTotal</td>
            <td class="numero">$ {{ number_format($subtotal, 2) }}</td>
        </tr>
    </table>

    {{-- Equipamiento --}}
    @if($equipamientoTotal > 0)
    <div class="seccion-titulo">EQUIPO</div>
    <table class="tabla-detalle">
        <thead>
            <tr>
                <th>Descripción</th>
                <th style="width: 10%;">Cant</th>
                <th style="width: 10%;">U/M</th>
                <th style="width: 15%;">Precio (USD)</th>
                <th style="width: 15%;">Total (USD)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyecto->lineasPresupuesto->where('tipo_linea', 'equipamiento') as $linea)
            <tr>
                <td>{{ $linea->descripcion }}</td>
                <td class="numero">{{ number_format($linea->cantidad, 2) }}</td>
                <td>{{ $linea->item->unidad_medida ?? 'u' }}</td>
                <td class="numero">{{ number_format($linea->costo_unitario, 2) }}</td>
                <td class="numero">{{ number_format($linea->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="4" style="text-align: right;">Subtotal:</td>
                <td class="numero">$ {{ number_format($equipamientoTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- Mano de Obra --}}
    @if($manoObraTotal > 0)
    <div class="seccion-titulo">MANO DE OBRA</div>
    <table class="tabla-detalle">
        <tbody>
            @foreach($proyecto->lineasPresupuesto->where('tipo_linea', 'mano_obra') as $linea)
            <tr>
                <td>{{ $linea->descripcion }}</td>
                <td class="numero" colspan="4">{{ number_format($linea->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="4" style="text-align: right;">Subtotal:</td>
                <td class="numero">$ {{ number_format($manoObraTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- Materiales --}}
    @if($materialesTotal > 0)
    <div class="seccion-titulo">MATERIALES</div>
    <table class="tabla-detalle">
        <thead>
            <tr>
                <th>Descripción</th>
                <th style="width: 10%;">Cant</th>
                <th style="width: 10%;">U/M</th>
                <th style="width: 15%;">Precio (USD)</th>
                <th style="width: 15%;">Total (USD)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyecto->lineasPresupuesto->where('tipo_linea', 'material') as $linea)
            <tr>
                <td>{{ $linea->descripcion }}</td>
                <td class="numero">{{ number_format($linea->cantidad, 2) }}</td>
                <td>{{ $linea->item->unidad_medida ?? 'u' }}</td>
                <td class="numero">{{ number_format($linea->costo_unitario, 2) }}</td>
                <td class="numero">{{ number_format($linea->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="4" style="text-align: right;">Subtotal:</td>
                <td class="numero">$ {{ number_format($materialesTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- Alimentación --}}
    @if($alimentacionTotal > 0)
    <div class="seccion-titulo">ALIMENTACIÓN</div>
    <table class="tabla-detalle">
        <thead>
            <tr>
                <th>Descripción</th>
                <th style="width: 10%;">Cant</th>
                <th style="width: 10%;">U/M</th>
                <th style="width: 15%;">Precio (USD)</th>
                <th style="width: 15%;">Total (USD)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyecto->lineasPresupuesto->where('tipo_linea', 'alimentacion') as $linea)
            <tr>
                <td>{{ $linea->descripcion }}</td>
                <td class="numero">{{ number_format($linea->cantidad, 2) }}</td>
                <td>días</td>
                <td class="numero">{{ number_format($linea->costo_unitario, 2) }}</td>
                <td class="numero">{{ number_format($linea->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="4" style="text-align: right;">Subtotal:</td>
                <td class="numero">$ {{ number_format($alimentacionTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- Transporte --}}
    @if($transporteTotal > 0)
    <div class="seccion-titulo">TRANSPORTE</div>
    <table class="tabla-detalle">
        <tbody>
            @foreach($proyecto->lineasPresupuesto->where('tipo_linea', 'transporte') as $linea)
            <tr>
                <td>{{ $linea->descripcion }}</td>
                <td class="numero" colspan="4">{{ number_format($linea->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="4" style="text-align: right;">Subtotal:</td>
                <td class="numero">$ {{ number_format($transporteTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- Total Final --}}
    <div class="total-final">
        TOTAL PRESUPUESTO: <span>$ {{ number_format($proyecto->presupuesto_total, 2) }} USD</span>
    </div>

    {{-- Notas --}}
    @if($proyecto->notas)
    <div class="notas">
        <strong>Notas:</strong>
        <p>{{ nl2br($proyecto->notas) }}</p>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>Presupuesto válido por 15 días a partir de la fecha de emisión</p>
        <p>Generado el {{ now()->format('d/m/Y H:i') }} | Dataplus S.R.L - Soluciones tecnológicas</p>
    </div>
</body>
</html>
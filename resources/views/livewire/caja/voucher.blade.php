<div class="voucher-wrapper">
<style>
    @media print {
        @page { size: A4; margin: 15mm; }
        body * { visibility: hidden; }
        body { background: white !important; height: auto !important; overflow: visible !important; }
        #voucher-imprimible, #voucher-imprimible * { visibility: visible; }
        #voucher-imprimible {
            position: absolute;
            left: 0; top: 0; width: 100%; max-width: 100%;
            margin: 0 !important; padding: 0 !important; box-shadow: none !important;
        }
        .print-btn-container { display: none !important; }
    }
</style>

@php
    function numeroALetras($numero) {
        $formatter = new class {
            private $unidades = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
            private $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
            private $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];
            private $especiales = [11=>'ONCE', 12=>'DOCE', 13=>'TRECE', 14=>'CATORCE', 15=>'QUINCE', 16=>'DIECISEIS', 17=>'DIECISIETE', 18=>'DIECIOCHO', 19=>'DIECINUEVE', 21=>'VEINTIUNO', 22=>'VEINTIDOS', 23=>'VEINTITRES', 24=>'VEINTICUATRO', 25=>'VEINTICINCO', 26=>'VEINTISEIS', 27=>'VEINTISIETE', 28=>'VEINTIOCHO', 29=>'VEINTINUEVE'];

            public function convertir($num) {
                if ($num == 0) return 'CERO';
                if ($num == 100) return 'CIEN';
                
                $letras = '';
                
                if ($num >= 1000) {
                    $miles = floor($num / 1000);
                    $num = $num % 1000;
                    if ($miles == 1) $letras .= 'MIL ';
                    else $letras .= $this->convertir($miles) . ' MIL ';
                }
                
                if ($num >= 100) {
                    $centenas = floor($num / 100);
                    $num = $num % 100;
                    $letras .= $this->centenas[$centenas] . ' ';
                }
                
                if ($num >= 10 && $num <= 29) {
                    if (isset($this->especiales[$num])) {
                        $letras .= $this->especiales[$num] . ' ';
                    } else {
                        $letras .= $this->decenas[floor($num / 10)] . ' ';
                    }
                    $num = 0;
                } elseif ($num >= 30) {
                    $decena = floor($num / 10);
                    $num = $num % 10;
                    $letras .= $this->decenas[$decena];
                    if ($num > 0) $letras .= ' Y ';
                    else $letras .= ' ';
                }
                
                if ($num > 0) {
                    $letras .= $this->unidades[$num] . ' ';
                }
                
                return trim($letras);
            }
        };
        
        return $formatter->convertir(floor($numero));
    }
@endphp

<div class="print-btn-container mb-4 text-center mt-6">
    <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg shadow-md flex items-center gap-2 mx-auto transition-colors">
        <span class="material-symbols-outlined">print</span>
        Imprimir Comprobante
    </button>
</div>

<div id="voucher-imprimible" class="max-w-4xl mx-auto bg-white text-black p-8 shadow-xl print:shadow-none mb-12 font-sans text-sm border border-zinc-200">
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start mb-8 gap-6 md:gap-0">
        <div class="w-full md:w-7/12 pr-4">
            <h1 class="font-extrabold text-2xl uppercase tracking-tight mb-1 text-black">{{ config('app.name', 'VetCoressen') }} S.A.C.</h1>
            <p class="font-bold text-xs uppercase mb-2 text-zinc-800">Veterinaria y Pet Shop</p>
            <p class="text-xs text-zinc-800 leading-snug mb-0.5">Av. Principal 123, Ciudad, Departamento, Perú</p>
            <p class="text-xs text-zinc-800 leading-snug">Tel: 01-1234567 | Correo: contacto@vetcoressen.com</p>
        </div>
        <div class="w-full md:w-5/12">
            <div class="border-2 border-black rounded-lg text-center py-4 px-2">
                <p class="font-bold text-lg text-black">R.U.C. 20123456789</p>
                @php
                    $isFactura = str_contains(strtolower($venta->tipo_comprobante), 'factura') || (strlen(preg_replace('/[^0-9]/', '', $venta->cliente->numero_documento ?? '')) === 11 && str_starts_with($venta->cliente->numero_documento ?? '', '20'));
                    $tipoDoc = $isFactura ? 'FACTURA ELECTRÓNICA' : 'BOLETA DE VENTA ELECTRÓNICA';
                    $serie = $isFactura ? 'F001' : 'B001';
                    $numero = str_pad($venta->numero_comprobante ?? $venta->id, 6, '0', STR_PAD_LEFT);
                @endphp
                <p class="font-bold text-xl uppercase bg-zinc-100 border-y-2 border-black my-2 py-1.5 text-black">{{ $tipoDoc }}</p>
                <p class="font-bold text-xl tracking-widest text-black">{{ $serie }}-{{ $numero }}</p>
            </div>
        </div>
    </div>

    {{-- CLIENT INFO --}}
    <div class="border border-black rounded-lg p-4 mb-6 text-xs text-black">
        <div class="grid grid-cols-12 gap-y-2">
            <div class="col-span-12 md:col-span-3 font-bold">Fecha de Emisión</div>
            <div class="col-span-12 md:col-span-9 uppercase md:before:content-[':\00a0']">{{ $venta->created_at->setTimezone('America/Lima')->format('d/m/Y h:i A') }}</div>
            
            <div class="col-span-12 md:col-span-3 font-bold">Señor(es)</div>
            <div class="col-span-12 md:col-span-9 uppercase md:before:content-[':\00a0']">{{ $venta->cliente->nombre_completo ?? 'CLIENTE GENERAL / PUBLICO EN GENERAL' }}</div>
            
            <div class="col-span-12 md:col-span-3 font-bold">{{ $isFactura ? 'RUC' : 'DNI/CE' }}</div>
            <div class="col-span-12 md:col-span-9 md:before:content-[':\00a0']">{{ $venta->cliente->numero_documento ?? '00000000' }}</div>
            
            <div class="col-span-12 md:col-span-3 font-bold">Dirección del Cliente</div>
            <div class="col-span-12 md:col-span-9 uppercase md:before:content-[':\00a0']">{{ $venta->cliente->direccion ?? '-' }}</div>
            
            <div class="col-span-12 md:col-span-3 font-bold">Tipo de Moneda</div>
            <div class="col-span-12 md:col-span-9 md:before:content-[':\00a0']">SOLES</div>
            
            <div class="col-span-12 md:col-span-3 font-bold">Observación</div>
            <div class="col-span-12 md:col-span-9 uppercase md:before:content-[':\00a0']">Pago mediante {{ str_replace('_', ' ', $venta->payment_method ?? 'EFECTIVO') }} - Atendido por: {{ $venta->cajero->name ?? 'Cajero' }}</div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="border border-black rounded-lg mb-6 overflow-hidden">
        <table class="w-full text-xs text-left text-black">
            <thead class="border-b border-black bg-zinc-100">
                <tr>
                    <th class="py-2 px-2 md:px-3 font-bold text-center border-r border-black w-16 md:w-24">Cantidad</th>
                    <th class="py-2 px-2 md:px-3 font-bold text-center border-r border-black w-24 md:w-32">Unidad Medida</th>
                    <th class="py-2 px-2 md:px-3 font-bold border-r border-black">Descripción</th>
                    <th class="py-2 px-2 md:px-3 font-bold text-right border-r border-black w-24 md:w-32">Valor Unitario</th>
                    <th class="py-2 px-2 md:px-3 font-bold text-right w-24 md:w-32">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                <tr class="border-b border-zinc-300 last:border-b-0">
                    <td class="py-2 px-2 md:px-3 text-center border-r border-black">{{ $detalle->quantity }}</td>
                    <td class="py-2 px-2 md:px-3 text-center border-r border-black">NIU/UNIDAD</td>
                    <td class="py-2 px-2 md:px-3 border-r border-black uppercase">{{ $detalle->producto?->name ?? ($detalle->description ?? 'SERVICIO VETERINARIO') }}</td>
                    <td class="py-2 px-2 md:px-3 text-right border-r border-black">{{ number_format($detalle->precio_final_unitario, 2) }}</td>
                    <td class="py-2 px-2 md:px-3 text-right">{{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- TOTALS & FOOTER --}}
    <div class="flex flex-col md:flex-row justify-between items-start gap-6">
        {{-- Left side: Amount in words and SUNAT text --}}
        <div class="w-full md:w-7/12 flex flex-col justify-between">
            <div class="mb-4">
                <p class="text-xs uppercase"><span class="font-bold">SON:</span> {{ numeroALetras($venta->total) }} Y {{ explode('.', number_format($venta->total, 2, '.', ''))[1] }}/100 SOLES</p>
            </div>
            
            <div class="border border-black rounded-lg p-3 text-[10px] text-center mt-auto uppercase font-medium">
                Esta es una representación impresa de la {{ strtolower($tipoDoc) }}, generada en el Sistema de SUNAT. Puede verificarla utilizando su clave SOL.
            </div>
        </div>
        
        {{-- Right side: Totals Box --}}
        <div class="w-full md:w-5/12 border border-black rounded-lg text-xs overflow-hidden">
            @php
                $total = $venta->total;
                $subtotal = $total / 1.18;
                $igv = $total - $subtotal;
            @endphp
            <div class="grid grid-cols-2 border-b border-zinc-300">
                <div class="py-1.5 px-3 font-bold text-right border-r border-zinc-300">Op. Gravadas :</div>
                <div class="py-1.5 px-3 text-right">S/ {{ number_format($subtotal, 2) }}</div>
            </div>
            <div class="grid grid-cols-2 border-b border-zinc-300">
                <div class="py-1.5 px-3 font-bold text-right border-r border-zinc-300">Op. Inafectas :</div>
                <div class="py-1.5 px-3 text-right">S/ 0.00</div>
            </div>
            <div class="grid grid-cols-2 border-b border-zinc-300">
                <div class="py-1.5 px-3 font-bold text-right border-r border-zinc-300">Op. Exoneradas :</div>
                <div class="py-1.5 px-3 text-right">S/ 0.00</div>
            </div>
            <div class="grid grid-cols-2 border-b border-zinc-300">
                <div class="py-1.5 px-3 font-bold text-right border-r border-zinc-300">IGV (18%) :</div>
                <div class="py-1.5 px-3 text-right">S/ {{ number_format($igv, 2) }}</div>
            </div>
            <div class="grid grid-cols-2 bg-zinc-100">
                <div class="py-2 px-3 font-bold text-right border-r border-zinc-300">Importe Total :</div>
                <div class="py-2 px-3 text-right font-bold text-sm">S/ {{ number_format($total, 2) }}</div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="voucher-wrapper bg-zinc-100 dark:bg-zinc-900 min-h-screen w-full pt-36 sm:pt-40 pb-12 px-4 relative overflow-y-auto">
<style>
    @media print {
        @page { size: A4 portrait; margin: 15mm 20mm; }
        body { 
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important; 
            background: white !important;
        }
        body * { visibility: hidden; }
        body, main { background: white !important; height: auto !important; overflow: visible !important; position: static !important; }
        #voucher-imprimible, #voucher-imprimible * { visibility: visible; }
        #voucher-imprimible {
            position: absolute;
            left: 0; top: 0; width: 100%;
            margin: 0 !important; padding: 0 !important; box-shadow: none !important; border: none !important;
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

    $isFactura = str_contains(strtolower($venta->tipo_comprobante), 'factura') || (strlen(preg_replace('/[^0-9]/', '', $venta->cliente->numero_documento ?? '')) === 11 && str_starts_with($venta->cliente->numero_documento ?? '', '20'));
    $tipoDoc = $isFactura ? 'FACTURA ELECTRÓNICA' : 'BOLETA DE VENTA ELECTRÓNICA';
    $serie = $isFactura ? 'F001' : 'B001';
    $numero = str_pad($venta->numero_comprobante ?? $venta->id, 8, '0', STR_PAD_LEFT);
    $clinica = $venta->clinica ?? \App\Models\Clinic::first();
@endphp

<div class="w-full max-w-[800px] mx-auto flex flex-col gap-6 mt-10">

    <div id="voucher-imprimible" class="w-full bg-white text-zinc-800 p-12 font-sans text-sm rounded-[2rem] shadow-2xl shadow-zinc-200/50 border border-zinc-200/80 print:shadow-none print:border-none print:rounded-none mx-auto relative overflow-hidden">
        
        {{-- Header --}}
        <div class="flex justify-between items-start mb-8">
            <div class="w-2/3 pr-6">
                <div class="flex items-center gap-4 mb-4">
                    @php
                        $logo = $clinica?->logo ? asset('storage/' . $clinica->logo) : asset('img/logo.png');
                    @endphp
                    <img src="{{ $logo }}" onerror="this.style.display='none'" class="h-16 w-auto object-contain">
                    <div>
                        <h1 class="font-extrabold text-3xl tracking-tight text-zinc-900 leading-none">{{ mb_strtoupper($clinica?->razon_social ?? config('app.name', 'VetCoressen') . ' S.A.C.') }}</h1>
                        <p class="font-bold text-sm uppercase text-zinc-600 mt-1.5">{{ $clinica?->name ?? 'Veterinaria y Pet Shop' }}</p>
                    </div>
                </div>
                <div class="text-[13px] text-zinc-700 space-y-1.5 pl-1">
                    <p class="uppercase"><span class="font-bold text-zinc-900 mr-1">Dirección:</span> {{ $clinica?->address ?: '-' }}</p>
                    <p class="uppercase"><span class="font-bold text-zinc-900 mr-1">Teléfono:</span> {{ $clinica?->phone ?: '-' }}</p>
                    <p class="uppercase"><span class="font-bold text-zinc-900 mr-1">Correo:</span> {{ $clinica?->email ?: '-' }}</p>
                </div>
            </div>
            
            <div class="w-1/3">
                <div class="border border-zinc-300 rounded-xl text-center overflow-hidden">
                    <div class="py-3 px-2 bg-zinc-50 border-b border-zinc-300">
                        <p class="font-bold text-lg text-zinc-900">RUC: {{ $clinica?->ruc ?: '20123456789' }}</p>
                    </div>
                    <div class="bg-zinc-800 text-white py-2" style="-webkit-print-color-adjust: exact; background-color: #27272a !important;">
                        <p class="font-bold text-sm uppercase tracking-wider">{{ $tipoDoc }}</p>
                    </div>
                    <div class="py-3 px-2 bg-white">
                        <p class="font-bold text-xl tracking-widest text-zinc-900">{{ $serie }}-{{ $numero }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Client Details --}}
        <div class="bg-zinc-50/80 rounded-2xl p-6 mb-8 border border-zinc-200 shadow-sm">
            <div class="grid grid-cols-12 gap-y-4 gap-x-6 text-[13px]">
                <div class="col-span-12 sm:col-span-6 grid grid-cols-12 gap-3">
                    <div class="col-span-5 font-bold text-zinc-700">Fecha de Emisión:</div>
                    <div class="col-span-7 uppercase text-zinc-900 font-semibold">{{ $venta->created_at->setTimezone('America/Lima')->format('d/m/Y') }}</div>
                    
                    <div class="col-span-5 font-bold text-zinc-700">Señor(es):</div>
                    <div class="col-span-7 uppercase text-zinc-900 font-semibold">{{ $venta->cliente->nombre_completo ?? 'CLIENTE GENERAL / PUBLICO EN GENERAL' }}</div>
                    
                    <div class="col-span-5 font-bold text-zinc-700">Dirección:</div>
                    <div class="col-span-7 uppercase text-zinc-900 font-semibold">{{ $venta->cliente->address ?: '-' }}</div>
                </div>
                
                <div class="col-span-12 sm:col-span-6 grid grid-cols-12 gap-3">
                    <div class="col-span-5 font-bold text-zinc-700">Forma de Pago:</div>
                    <div class="col-span-7 uppercase text-zinc-900 font-semibold">CONTADO</div>
                    
                    <div class="col-span-5 font-bold text-zinc-700">{{ $isFactura ? 'RUC' : 'DNI/CE' }}:</div>
                    <div class="col-span-7 uppercase text-zinc-900 font-semibold">{{ $venta->cliente->numero_documento ?? '00000000' }}</div>
                    
                    <div class="col-span-5 font-bold text-zinc-700">Moneda:</div>
                    <div class="col-span-7 uppercase text-zinc-900 font-semibold">SOLES</div>
                </div>

                <div class="col-span-12 grid grid-cols-12 gap-2 border-t border-zinc-200 pt-3 mt-1">
                    <div class="col-span-12 sm:col-span-2 font-semibold text-zinc-600">Observación:</div>
                    <div class="col-span-12 sm:col-span-10 uppercase text-zinc-900 font-medium">PAGO EN {{ str_replace('_', ' ', $venta->payment_method ?? 'EFECTIVO') }} - {{ $venta->cajero->name ?? 'CAJERO' }}</div>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="mb-8 rounded-xl overflow-hidden border border-zinc-300">
            <table class="w-full text-[13px] text-left">
                <thead class="text-white font-bold bg-zinc-800 dark:bg-zinc-900" style="-webkit-print-color-adjust: exact; background-color: #27272a !important; color: white !important;">
                    <tr>
                        <th class="py-4 px-5 text-center w-24 border-r border-zinc-700" style="background-color: #27272a !important; border-color: #3f3f46 !important; color: #ffffff !important;">CANT.</th>
                        <th class="py-4 px-5 text-center w-28 border-r border-zinc-700" style="background-color: #27272a !important; border-color: #3f3f46 !important; color: #ffffff !important;">U. MEDIDA</th>
                        <th class="py-4 px-5 border-r border-zinc-700" style="background-color: #27272a !important; border-color: #3f3f46 !important; color: #ffffff !important;">DESCRIPCIÓN</th>
                        <th class="py-4 px-5 text-right w-32 border-r border-zinc-700" style="background-color: #27272a !important; border-color: #3f3f46 !important; color: #ffffff !important;">V. UNIT.</th>
                        <th class="py-4 px-5 text-right w-32" style="background-color: #27272a !important; color: #ffffff !important;">IMPORTE</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @foreach($venta->detalles as $detalle)
                    <tr class="bg-white">
                        <td class="py-4 px-5 text-center font-bold text-zinc-800">{{ number_format($detalle->quantity, 0) }}</td>
                        <td class="py-4 px-5 text-center text-zinc-600 font-medium">NIU/UNIDAD</td>
                        <td class="py-4 px-5 uppercase text-zinc-900 font-bold">{{ $detalle->producto?->name ?? ($detalle->description ?? 'SERVICIO VETERINARIO') }}</td>
                        <td class="py-4 px-5 text-right text-zinc-700 font-medium">{{ number_format($detalle->precio_final_unitario, 2) }}</td>
                        <td class="py-4 px-5 text-right font-extrabold text-zinc-900">{{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer Totals --}}
        <div class="flex flex-col sm:flex-row items-start justify-between gap-8 mb-8">
            <div class="w-full sm:w-7/12">
                <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-200 mb-4">
                    <p class="font-bold text-xs uppercase text-zinc-700 leading-relaxed">
                        SON: {{ numeroALetras($venta->total) }} Y {{ explode('.', number_format($venta->total, 2, '.', ''))[1] }}/100 SOLES
                    </p>
                </div>
                <div class="text-[10px] text-zinc-500">
                    <p>Representación impresa de comprobante electrónico.</p>
                    <p>Consulte su validez en www.sunat.gob.pe</p>
                </div>
            </div>
            
            <div class="w-full sm:w-5/12">
                <div class="bg-zinc-50 rounded-xl border border-zinc-200 p-4 text-xs">
                    @php
                        $total = $venta->total;
                        $subtotal = $total / 1.18;
                        $igv = $total - $subtotal;
                    @endphp
                    <div class="flex justify-between py-1.5">
                        <div class="font-medium text-zinc-600">Sub Total Ventas:</div>
                        <div class="font-semibold text-zinc-900 text-right">S/ {{ number_format($subtotal, 2) }}</div>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <div class="font-medium text-zinc-600">Descuentos:</div>
                        <div class="font-semibold text-zinc-900 text-right">S/ 0.00</div>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-zinc-200 mb-1.5 pb-2">
                        <div class="font-medium text-zinc-600">IGV (18%):</div>
                        <div class="font-semibold text-zinc-900 text-right">S/ {{ number_format($igv, 2) }}</div>
                    </div>
                    <div class="flex justify-between py-2 mt-1 bg-zinc-800 text-white rounded-lg px-3" style="-webkit-print-color-adjust: exact; background-color: #27272a !important; color: white !important;">
                        <div class="font-bold uppercase tracking-wide" style="color: white !important;">Importe Total:</div>
                        <div class="font-bold text-right" style="color: white !important;">S/ {{ number_format($total, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Document Verification Text --}}
        <div class="flex justify-center items-center gap-4 text-center mt-10 pt-6 border-t border-zinc-200">
            <div class="text-center max-w-sm mx-auto">
                <p class="text-[10px] text-zinc-500 leading-tight">Este comprobante puede ser verificado utilizando la clave SOL en el sistema de SUNAT.</p>
            </div>
        </div>
        
    </div>
    
    <div class="fixed bottom-6 right-6 z-[100] print-btn-container" style="z-index: 9999;">
        <button onclick="window.print()" class="w-14 h-14 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center transition-all">
            <span class="material-symbols-outlined text-[24px]">print</span>
        </button>
    </div>
</div>
</div>

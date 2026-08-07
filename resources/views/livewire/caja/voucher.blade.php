<style>
    @media print {
        @page { margin: 0; }
        body * {
            visibility: hidden;
        }
        body {
            height: auto !important;
            overflow: visible !important;
            display: block !important;
            background: white !important;
        }
        #voucher-imprimible, #voucher-imprimible * {
            visibility: visible;
        }
        #voucher-imprimible {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            max-width: 100%;
            margin: 0 !important;
            padding: 10px !important;
            box-shadow: none !important;
        }
    }
</style>

<div id="voucher-imprimible" class="max-w-xs mx-auto bg-white text-black p-6 shadow-lg rounded-xl print:shadow-none print:p-0 my-8">
    <div class="text-center mb-6">
        <h2 class="font-bold text-xl text-black">{{ config('app.name', 'VetCoressen') }}</h2>
        <p class="text-sm text-zinc-800">RUC: 20123456789</p>
        <p class="text-sm text-zinc-800">Av. Principal 123, Ciudad</p>
        <p class="text-sm text-zinc-800">Tel: 01-1234567</p>
    </div>

    <div class="text-center mb-6 border-y border-dashed border-zinc-400 py-3">
        <h3 class="font-bold text-lg uppercase text-black">{{ $venta->tipo_comprobante ?? 'BOLETA ELECTRÓNICA' }}</h3>
        <p class="text-sm font-mono tracking-widest text-black">{{ $venta->serie_comprobante ?? 'B001' }}-{{ str_pad($venta->numero_comprobante ?? $venta->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="mb-4 space-y-1 text-sm font-mono text-black">
        <p><span class="font-semibold">FECHA:</span> {{ $venta->created_at->setTimezone('America/Lima')->format('d/m/Y h:i A') }}</p>
        <p><span class="font-semibold">CLIENTE:</span> {{ $venta->cliente->nombre_completo ?? 'Cliente General' }}</p>
        <p><span class="font-semibold">DOC:</span> {{ $venta->cliente->numero_documento ?? '-' }}</p>
        <p><span class="font-semibold">CAJERO:</span> {{ $venta->cajero->nombre_completo ?? 'Administrador' }}</p>
        <p><span class="font-semibold">PAGO:</span> {{ str_replace('_', ' ', $venta->payment_method ?? 'Efectivo') }}</p>
    </div>

    <div class="border-b border-dashed border-zinc-400 pb-2 mb-2 font-mono text-xs text-black">
        <div class="grid grid-cols-12 font-bold mb-1">
            <div class="col-span-2">CANT</div>
            <div class="col-span-6">DESCRIPCIÓN</div>
            <div class="col-span-4 text-right">TOTAL</div>
        </div>
        @foreach($venta->detalles as $detalle)
        <div class="grid grid-cols-12 mb-1">
            <div class="col-span-2">{{ $detalle->quantity }}</div>
            <div class="col-span-6 pr-1">{{ $detalle->producto?->type ? '['.substr($detalle->producto->type, 0, 1).'] ' : '' }}{{ $detalle->producto->name ?? ($detalle->description ?? 'Servicio') }}</div>
            <div class="col-span-4 text-right">S/ {{ number_format($detalle->subtotal, 2) }}</div>
        </div>
        @endforeach
    </div>

    <div class="space-y-1 text-sm font-mono text-right mb-6 text-black">
        @php
            $subtotal = $venta->total / 1.18;
            $igv = $venta->total - $subtotal;
        @endphp
        <p>SUBTOTAL: S/ {{ number_format($subtotal, 2) }}</p>
        <p>IGV (18%): S/ {{ number_format($igv, 2) }}</p>
        <p class="font-bold text-lg mt-2">TOTAL: S/ {{ number_format($venta->total, 2) }}</p>
    </div>

    <div class="text-center font-mono text-xs text-zinc-800 mb-6 space-y-2">
        <p>¡GRACIAS POR SU PREFERENCIA!</p>
        <div class="mx-auto w-32 h-32 flex items-center justify-center mt-2">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=128x128&data={{ urlencode(route('caja.voucher', $venta->id)) }}" alt="QR Code" class="w-full h-full" />
        </div>
    </div>

    <div class="flex justify-center gap-4 print:hidden mt-8">
        <a href="{{ route('caja.index') }}" class="px-4 py-2 bg-zinc-200 text-zinc-900 hover:bg-zinc-300 font-semibold rounded-lg transition-colors flex items-center justify-center" wire:navigate>Volver</a>
        <button type="button" class="px-4 py-2 bg-emerald-600 text-white hover:bg-emerald-700 font-semibold rounded-lg transition-colors flex items-center justify-center gap-2" onclick="window.print()">
            <span class="material-symbols-outlined icon-sm">print</span>
            <span>Imprimir Voucher</span>
        </button>
    </div>
</div>

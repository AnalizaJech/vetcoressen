<?php

namespace App\Livewire\Caja;

use App\Models\Sale;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

// Panel de caja: resumen de ventas del día y acceso al POS
#[Layout('components.layouts.app')]
#[Title('Caja')]
class CajaIndex extends Component
{
    use WithPagination;

    // Resumen
    public string $filtroTiempo = 'hoy'; // 'hoy', 'semana', 'mes', 'anio'
    public float $totalVentasHoy = 0;
    public int $cantidadVentasHoy = 0;
    public float $totalEfectivo = 0;
    public float $totalTarjeta = 0;
    public float $totalDigital = 0; // Yape/Plin/Transferencia

    public ?int $ventaAnularId = null;
    public ?int $ventaVerId = null;
    public ?Sale $ventaVer = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view_sales'), 403);
        $this->cargarResumen();
    }

    // Calcular resumen de ventas del periodo actual
    public function cargarResumen(): void
    {
        $hoy = now()->toDateString();
        $fechaInicio = match ($this->filtroTiempo) {
            'hoy' => now()->startOfDay(),
            'semana' => now()->startOfWeek(),
            'mes' => now()->startOfMonth(),
            'anio' => now()->startOfYear(),
            default => now()->startOfDay(),
        };

        $ventasPeriodo = Sale::whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $hoy)
            ->where('status', 'PAGADO')
            ->get();

        $this->cantidadVentasHoy = $ventasPeriodo->count();
        $this->totalVentasHoy = $ventasPeriodo->sum('total');
        $this->totalEfectivo = $ventasPeriodo->where('payment_method', 'EFECTIVO')->sum('total');
        $this->totalTarjeta = $ventasPeriodo->where('payment_method', 'TARJETA')->sum('total');
        $this->totalDigital = $ventasPeriodo->whereIn('payment_method', ['YAPE_PLIN', 'TRANSFERENCIA'])->sum('total');
    }

    public function updatedFiltroTiempo(): void
    {
        $this->cargarResumen();
    }

    public function verVenta(int $id): void
    {
        $this->ventaVerId = $id;
        $this->ventaVer = Sale::with(['detalles.producto', 'cliente', 'cajero'])->findOrFail($id);
    }

    public function anularVentaConfirmada()
    {
        if (!$this->ventaAnularId) return;

        $venta = Sale::with('detalles')->findOrFail($this->ventaAnularId);

        if ($venta->status === 'ANULADO') {
            session()->flash('mensaje', 'La venta ya está anulada.');
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($venta, &$nc) {
            $venta->update(['status' => 'ANULADO']);

            // Crear nota de crédito
            $nc = $venta->replicate();
            $nc->parent_sale_id = $venta->id;
            $nc->is_credit_note = true;
            $nc->total = -$venta->total;
            $nc->subtotal = -$venta->subtotal;
            $nc->igv = -$venta->igv;
            $nc->tipo_comprobante = 'NOTA_CREDITO'; 
            $nc->save();

            // Devolver stock
            foreach ($venta->detalles as $detalle) {
                $producto = \App\Models\Product::find($detalle->product_id);
                if ($producto && $producto->type !== 'SERVICIO') {
                    $producto->increment('current_stock', $detalle->quantity);

                    \App\Models\InventoryMovement::create([
                        'clinic_id'           => 1,
                        'product_id'          => $producto->id,
                        'user_id'             => auth()->id(),
                        'type'                => 'ENTRADA_AJUSTE',
                        'quantity'            => $detalle->quantity,
                        'costo_unitario'      => null,
                        'reference_document'  => "Anulación Venta #{$venta->id}",
                        'stock_anterior'      => $producto->current_stock - $detalle->quantity,
                        'stock_posterior'     => $producto->current_stock,
                        'referencia_tipo'     => 'venta_anulada',
                        'referencia_id'       => $venta->id,
                        'notes'               => "Devolución por anulación de venta",
                    ]);
                }
            }
        });

        // Enviar a Nubefact si fue Boleta o Factura
        if (in_array($venta->tipo_comprobante, ['BOLETA', 'FACTURA'])) {
            $nubefact = new \App\Services\NubefactService();
            $respuesta = $nubefact->emitirComprobante($nc);
            
            if (isset($respuesta['exito']) && $respuesta['exito']) {
                $nc->update([
                    'nubefact_enlace_pdf' => $respuesta['enlace_del_pdf'] ?? null,
                    'nubefact_enlace_xml' => $respuesta['enlace_del_xml'] ?? null,
                    'nubefact_enlace_cdr' => $respuesta['enlace_del_cdr'] ?? null,
                    'nubefact_sunat_ticket' => $respuesta['sunat_ticket_numero'] ?? null,
                ]);
            }
        }

        session()->flash('mensaje', 'Venta anulada correctamente. Stock devuelto.');
        $this->ventaAnularId = null;
        $this->cargarResumen();
    }

    public function render()
    {
        $hoy = now()->toDateString();
        $fechaInicio = match ($this->filtroTiempo) {
            'hoy' => now()->startOfDay(),
            'semana' => now()->startOfWeek(),
            'mes' => now()->startOfMonth(),
            'anio' => now()->startOfYear(),
            default => now()->startOfDay(),
        };

        // Ventas del periodo
        $ventasRecientes = Sale::with('cliente', 'cajero')
            ->whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $hoy)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.caja.caja-index', [
            'ventasRecientes' => $ventasRecientes,
        ]);
    }
}

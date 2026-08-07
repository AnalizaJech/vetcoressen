<?php

namespace App\Services;

use App\Models\Sale;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NubefactService
{
    protected string $url;
    protected string $token;
    protected bool $mock;

    public function __construct()
    {
        $this->url = config('services.nubefact.url', env('NUBEFACT_URL', ''));
        $this->token = config('services.nubefact.token', env('NUBEFACT_TOKEN', ''));
        $this->mock = empty($this->token);
    }

    /**
     * Emite un comprobante electrónico (Factura o Boleta)
     * Si no hay token configurado, funciona en modo MOCK.
     */
    public function emitirComprobante(Sale $venta): array
    {
        if ($this->mock) {
            Log::info("Nubefact MOCK: Emitiendo {$venta->tipo_comprobante} para Venta #{$venta->id}");
            $serie = $venta->tipo_comprobante === 'FACTURA' ? 'F001' : 'B001';
            $numero = str_pad($venta->id, 6, '0', STR_PAD_LEFT);
            return [
                'exito' => true,
                'enlace_del_pdf' => route('caja.voucher', $venta->id),
                'enlace_del_xml' => "#",
                'enlace_del_cdr' => "#",
                'sunat_ticket_numero' => "TICKET-MOCK-{$venta->id}",
            ];
        }

        // Lógica real de integración con Nubefact
        $data = $this->prepararDatos($venta);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ])->post($this->url, $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Error en Nubefact', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return [
                'exito' => false,
                'error' => $response->json('errors') ?? 'Error desconocido de Nubefact',
            ];
        } catch (\Exception $e) {
            Log::error('Excepción al conectar con Nubefact: ' . $e->getMessage());
            return [
                'exito' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function prepararDatos(Sale $venta): array
    {
        $cliente = $venta->cliente;
        
        $tipoComprobante = $venta->tipo_comprobante === 'FACTURA' ? 1 : 2;
        $serie = $venta->tipo_comprobante === 'FACTURA' ? "F001" : "B001";

        if ($venta->is_credit_note) {
            $tipoComprobante = 3; // Nota de Crédito
            // Asumimos que parent_sale_id apunta a la venta original
            $ventaOriginal = \App\Models\Sale::find($venta->parent_sale_id);
            $tipoDocModifica = $ventaOriginal && $ventaOriginal->tipo_comprobante === 'FACTURA' ? 1 : 2;
            $serieModifica = $ventaOriginal && $ventaOriginal->tipo_comprobante === 'FACTURA' ? "F001" : "B001";
            $numeroModifica = $ventaOriginal ? $ventaOriginal->id : "0";
            
            // Requeridos para NC
            $extraData = [
                "documento_que_se_modifica_tipo" => $tipoDocModifica,
                "documento_que_se_modifica_serie" => $serieModifica,
                "documento_que_se_modifica_numero" => $numeroModifica,
                "tipo_de_nota_de_credito" => 1, // 1: Anulación de la operación
                "motivo_o_sustento_de_nota_de_credito" => "Anulación de venta",
            ];
            
            // Para la NC, la serie también cambia según si afecta Factura (F) o Boleta (B)
            $serie = $ventaOriginal && $ventaOriginal->tipo_comprobante === 'FACTURA' ? "FC01" : "BC01";
        } else {
            $extraData = [];
        }

        // Mapeo básico para el ejemplo
        $data = [
            "operacion" => "generar_comprobante",
            "tipo_de_comprobante" => $tipoComprobante,
            "serie" => $serie,
            "numero" => $venta->id,
            "sunat_transaction" => 1,
            "cliente_tipo_de_documento" => $cliente ? ($cliente->document_type === 'RUC' ? 6 : 1) : 0,
            "cliente_numero_de_documento" => $cliente ? $cliente->document_number : "00000000",
            "cliente_denominacion" => $cliente ? $cliente->first_name . ' ' . $cliente->last_name : "CLIENTE VARIOS",
            "cliente_direccion" => $cliente ? $cliente->address : "-",
            "cliente_email" => $cliente ? $cliente->email : "",
            "fecha_de_emision" => $venta->created_at->format('d-m-Y'),
            "moneda" => 1, // Soles
            "porcentaje_de_igv" => 18.00,
            "total_gravada" => abs($venta->subtotal),
            "total_igv" => abs($venta->igv),
            "total" => abs($venta->total),
            "detener_y_terminar" => true,
            "enviar_automaticamente_a_la_sunat" => true,
            "enviar_automaticamente_al_cliente" => false,
            "items" => $venta->detalles->map(function ($detalle) {
                return [
                    "unidad_de_medida" => "NIU",
                    "codigo" => "P" . $detalle->product_id,
                    "descripcion" => $detalle->description,
                    "cantidad" => abs($detalle->quantity),
                    "valor_unitario" => round(abs($detalle->unit_price) / 1.18, 2), // Sin IGV
                    "precio_unitario" => abs($detalle->unit_price), // Con IGV
                    "subtotal" => round((abs($detalle->unit_price) / 1.18) * abs($detalle->quantity), 2),
                    "tipo_de_igv" => $detalle->afecto_igv ? 1 : 8, // 1: Gravado, 8: Inafecto
                    "igv" => $detalle->afecto_igv ? round(abs($detalle->subtotal) - (abs($detalle->subtotal) / 1.18), 2) : 0,
                    "total" => abs($detalle->subtotal),
                    "anticipo_regularizacion" => false
                ];
            })->toArray()
        ];

        return array_merge($data, $extraData);
    }
}

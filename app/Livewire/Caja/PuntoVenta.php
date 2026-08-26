<?php

namespace App\Livewire\Caja;

use App\Models\Customer;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

// Punto de venta con carrito dinámico, cálculo IGV y Kardex automático
#[Layout('components.layouts.app')]
#[Title('Nueva Venta')]
class PuntoVenta extends Component
{
    // Búsqueda de producto
    public string $buscarProducto = '';
    public ?string $filtroTipo = '';

    // Carrito: array de items [{producto_id, nombre, cantidad, precio_unitario, afecto_igv, subtotal}]
    public array $carrito = [];

    // Datos de la venta
    public string $cliente_id = '';
    public string $tipo_comprobante = 'BOLETA';
    public string $metodo_pago = 'EFECTIVO';
    public string $notas = '';
    public bool $is_emergency = false;

    // Datos rápidos de facturación (si el cliente no tiene RUC)
    public string $nuevo_ruc = '';
    public string $nueva_razon_social = '';
    public string $nueva_direccion = '';

    // Vuelto calculation
    public $monto_recibido = '';
    public float $vuelto = 0;

    // Item a eliminar
    public ?int $itemAEliminar = null;

    // Totales calculados
    public float $subtotal = 0;
    public float $igv = 0;
    public float $total = 0;

    // Tasa de IGV (18% en Perú)
    const IGV_TASA = 0.18;

    public function mount(): void
    {
        abort_unless(auth()->user()->can('create_sales'), 403);
    }

    public function getActiveRegisterProperty()
    {
        return \App\Models\CashRegister::where('user_id', auth()->id())
            ->where('status', 'ABIERTA')
            ->first();
    }

    public function updatedClienteId()
    {
        if ($this->cliente_id) {
            $cliente = Customer::find($this->cliente_id);
            if ($cliente) {
                if ($cliente->tipo_documento === 'RUC') {
                    if (str_starts_with($cliente->numero_documento, '20')) {
                        $this->tipo_comprobante = 'FACTURA';
                    } else if (str_starts_with($cliente->numero_documento, '10')) {
                        if ($this->tipo_comprobante === 'NOTA_VENTA') {
                            $this->tipo_comprobante = 'BOLETA'; // Or Factura
                        }
                    }
                } else {
                    if ($this->tipo_comprobante === 'FACTURA') {
                        $this->tipo_comprobante = 'BOLETA';
                    }
                }
            }
        }
    }

    public function buscarRuc()
    {
        $this->validate(['nuevo_ruc' => 'required|digits:11']);

        try {
            // Usamos una API pública para obtener datos de RUC (apis.net.pe)
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get("https://api.apis.net.pe/v1/ruc?numero={$this->nuevo_ruc}");
            
            if ($response->successful()) {
                $data = $response->json();
                $this->nueva_razon_social = $data['nombre'] ?? '';
                $this->nueva_direccion = $data['direccion'] ?? '';
                session()->flash('mensaje_ruc', 'Datos obtenidos correctamente.');
            } else {
                session()->flash('mensaje_ruc', 'No se encontró el RUC o la API no responde.');
            }
        } catch (\Exception $e) {
            session()->flash('mensaje_ruc', 'Error al consultar RUC.');
        }
    }

    // Agregar producto al carrito
    public function agregarAlCarrito(int $productoId): void
    {
        $producto = Product::find($productoId);
        if (!$producto || !$producto->is_active) return;

        // Verificar stock disponible
        if ($producto->current_stock <= 0 && !$this->is_emergency && $producto->type !== 'SERVICIO') {
            session()->flash('mensaje', "Sin stock disponible para «{$producto->name}». (Activa Modo Emergencia si es estrictamente necesario)");
            return;
        }

        // Si ya está en el carrito, incrementar cantidad
        foreach ($this->carrito as &$item) {
            if ($item['product_id'] === $productoId) {
                if ($item['quantity'] >= $producto->current_stock && !$this->is_emergency && $producto->type !== 'SERVICIO') {
                    session()->flash('mensaje', "Stock insuficiente. Disponible: {$producto->current_stock}");
                    return;
                }
                $item['quantity']++;
                $item['subtotal'] = $item['quantity'] * $item['unit_price'];
                $this->recalcularTotales();
                return;
            }
        }
        unset($item);

        // Nuevo item en el carrito
        $this->carrito[] = [
            'product_id'    => $productoId,
            'name'         => $producto->name,
            'quantity'        => 1,
            'unit_price' => (float) $producto->base_imponible,
            'afecto_igv'      => $producto->tipo_afectacion_igv === 'Gravado',
            'subtotal'        => (float) $producto->base_imponible,
            'stock_disponible' => $producto->current_stock,
        ];

        $this->recalcularTotales();
        $this->buscarProducto = '';
    }

    // Actualizar cantidad de un item
    public function actualizarCantidad(int $index, int $cantidad): void
    {
        if (!isset($this->carrito[$index])) return;

        if ($cantidad <= 0) {
            $this->eliminarDelCarrito($index);
            return;
        }

        // Verificar stock
        if ($cantidad > $this->carrito[$index]['stock_disponible'] && !$this->is_emergency) {
            session()->flash('mensaje', 'Stock insuficiente.');
            return;
        }

        $this->carrito[$index]['quantity'] = $cantidad;
        $this->carrito[$index]['subtotal'] = $cantidad * $this->carrito[$index]['unit_price'];
        $this->recalcularTotales();
    }

    public function aumentarCantidad(int $index): void
    {
        if (!isset($this->carrito[$index])) return;
        $this->actualizarCantidad($index, $this->carrito[$index]['quantity'] + 1);
    }

    public function disminuirCantidad(int $index): void
    {
        if (!isset($this->carrito[$index])) return;
        $this->actualizarCantidad($index, $this->carrito[$index]['quantity'] - 1);
    }

    // Eliminar item del carrito
    public function eliminarDelCarrito(int $index): void
    {
        unset($this->carrito[$index]);
        $this->carrito = array_values($this->carrito);
        $this->recalcularTotales();
    }

    // Recalcular subtotal, IGV y total con redondeo estricto a 2 decimales
    public function recalcularTotales(): void
    {
        $this->subtotal = 0;
        $this->igv = 0;

        foreach ($this->carrito as $item) {
            $subItem = round((float) $item['subtotal'], 2);
            if ($item['afecto_igv']) {
                $baseItem = $subItem;
                $igvItem = round($subItem * self::IGV_TASA, 2);
                $this->subtotal += $baseItem;
                $this->igv += $igvItem;
            } else {
                $this->subtotal += $subItem;
            }
        }

        $this->subtotal = round($this->subtotal, 2);
        $this->igv = round($this->igv, 2);
        $this->total = round($this->subtotal + $this->igv, 2);
        $this->calcularVuelto();
    }

    public function updatedMontoRecibido()
    {
        if ($this->monto_recibido !== '' && is_numeric($this->monto_recibido)) {
            $this->monto_recibido = (string) round((float) $this->monto_recibido, 2);
        }
        $this->calcularVuelto();
    }

    public function updatedMetodoPago()
    {
        if ($this->metodo_pago !== 'EFECTIVO') {
            $this->monto_recibido = '';
            $this->vuelto = 0;
        }
    }

    public function calcularVuelto()
    {
        if ($this->metodo_pago === 'EFECTIVO') {
            $monto = ($this->monto_recibido !== '' && is_numeric($this->monto_recibido)) ? round((float) $this->monto_recibido, 2) : 0;
            $this->vuelto = round(max(0, $monto - $this->total), 2);
        } else {
            $this->vuelto = 0;
        }
    }

    // Procesar Venta
    public function procesarVenta()
    {
        if (empty($this->carrito)) {
            session()->flash('mensaje', 'El carrito está vacío.');
            return;
        }

        $activeRegister = $this->activeRegister;
        if (!$activeRegister) {
            session()->flash('mensaje', 'Debe abrir una caja antes de procesar ventas.');
            return;
        }

        $this->validate([
            'metodo_pago'      => 'required|in:EFECTIVO,TARJETA,TRANSFERENCIA,YAPE_PLIN',
            'tipo_comprobante' => 'required|in:NOTA_VENTA,BOLETA,FACTURA',
            'cliente_id'       => 'nullable|exists:customers,id',
        ]);
        
        if ($this->tipo_comprobante === 'FACTURA') {
            if (!$this->cliente_id) {
                // Verificar si llenó los datos manuales (Cliente General -> Nueva Empresa)
                if (empty($this->nuevo_ruc) || empty($this->nueva_razon_social)) {
                    session()->flash('mensaje', 'Para emitir FACTURA debe seleccionar una empresa o ingresar los datos del RUC manualmente.');
                    return;
                }
                
                // Crear nueva empresa (cliente)
                $cliente = new Customer();
                $cliente->tipo_documento = 'RUC';
                $cliente->numero_documento = $this->nuevo_ruc;
                $cliente->first_name = $this->nueva_razon_social;
                $cliente->last_name = ''; // Prevent null constraints si los hay
                if ($this->nueva_direccion) $cliente->address = $this->nueva_direccion;
                $cliente->save();
                
                $this->cliente_id = $cliente->id;
            } else {
                // Tiene cliente seleccionado, validar que tenga RUC
                $cliente = Customer::find($this->cliente_id);
                if (!$cliente || $cliente->tipo_documento !== 'RUC' || empty($cliente->numero_documento)) {
                    session()->flash('mensaje', 'El cliente seleccionado no tiene RUC. Deseleccione al cliente para ingresar un RUC manual o seleccione una empresa.');
                    return;
                }
            }
        } elseif ($this->tipo_comprobante === 'BOLETA') {
            if ($this->total >= 700 && !$this->cliente_id) {
                session()->flash('mensaje', 'Para montos mayores a S/ 700 en BOLETA, debe seleccionar un cliente con documento de identidad.');
                return;
            }
            if ($this->cliente_id) {
                $cliente = Customer::find($this->cliente_id);
                if ($cliente && empty($cliente->numero_documento)) {
                    session()->flash('mensaje', 'El cliente seleccionado no tiene documento de identidad registrado.');
                    return;
                }
                if ($cliente && $cliente->tipo_documento === 'RUC' && str_starts_with($cliente->numero_documento, '20')) {
                    session()->flash('mensaje', 'Una empresa con RUC 20 no puede tener boleta, debe emitir una Factura.');
                    return;
                }
            }
        } elseif ($this->tipo_comprobante === 'NOTA_VENTA') {
            if ($this->cliente_id) {
                $cliente = Customer::find($this->cliente_id);
                if ($cliente && $cliente->tipo_documento === 'RUC' && str_starts_with($cliente->numero_documento, '20')) {
                    session()->flash('mensaje', 'Una empresa con RUC 20 no puede tener nota de venta, debe emitir una Factura.');
                    return;
                }
            }
        }
        
        $venta = DB::transaction(function () use ($activeRegister) {
            // Crear la venta
            $venta = Sale::create([
                'clinic_id'       => 1,
                'customer_id'       => $this->cliente_id ?: null,
                'cajero_id'        => \Illuminate\Support\Facades\Auth::id(),
                'cash_register_id' => $activeRegister->id,
                'tipo_comprobante' => $this->tipo_comprobante,
                'subtotal'         => round($this->subtotal, 2),
                'igv'              => round($this->igv, 2),
                'total'            => round($this->total, 2),
                'payment_method'      => $this->metodo_pago,
                'status'           => 'PAGADO',
                'notes'            => $this->notas ?: null,
            ]);

            // Crear detalles y descontar stock (Kardex)
            foreach ($this->carrito as $item) {
                // Detalle de venta
                $baseItem = $item['subtotal'];
                $igvItem = $item['afecto_igv'] ? $item['subtotal'] * self::IGV_TASA : 0;
                $precio_final_unitario = $item['unit_price'] + ($item['afecto_igv'] ? $item['unit_price'] * self::IGV_TASA : 0);
                
                SaleDetail::create([
                    'sale_id'        => $venta->id,
                    'product_id'     => $item['product_id'],
                    'description'     => $item['name'],
                    'quantity'        => $item['quantity'],
                    'precio_final_unitario' => $precio_final_unitario,
                    'base_imponible' => $baseItem,
                    'igv_monto' => $igvItem,
                    'subtotal'        => $baseItem + $igvItem,
                ]);

                // Descontar stock del producto
                $producto = Product::find($item['product_id']);
                if ($producto) {
                    $stockAnterior = $producto->current_stock;
                    $producto->decrement('current_stock', $item['quantity']);

                    // Registrar movimiento Kardex
                    InventoryMovement::create([
                        'clinic_id'           => 1,
                        'product_id'          => $item['product_id'],
                        'user_id'           => \Illuminate\Support\Facades\Auth::id(),
                        'type'                 => 'SALIDA_VENTA',
                        'quantity'             => $item['quantity'],
                        'costo_unitario'       => $producto->costo_compra,
                        'reference_document' => "Venta #{$venta->id}",
                        'stock_anterior'       => $stockAnterior,
                        'stock_posterior'       => $stockAnterior - $item['quantity'],
                        'referencia_tipo'      => 'venta',
                        'referencia_id'        => $venta->id,
                        'notes'                => "Venta POS - {$this->tipo_comprobante}",
                    ]);
                }
            }

            // Integración Nubefact
            if (in_array($this->tipo_comprobante, ['BOLETA', 'FACTURA'])) {
                $nubefactService = new \App\Services\NubefactService();
                $response = $nubefactService->emitirComprobante($venta);

                if ($response['exito'] ?? false) {
                    $venta->update([
                        'nubefact_enlace_pdf' => $response['enlace_del_pdf'] ?? null,
                        'nubefact_enlace_xml' => $response['enlace_del_xml'] ?? null,
                        'nubefact_enlace_cdr' => $response['enlace_del_cdr'] ?? null,
                        'nubefact_sunat_ticket_numero' => $response['sunat_ticket_numero'] ?? null,
                    ]);
                } else {
                    $venta->update([
                        'nubefact_error' => is_array($response['error'] ?? null) ? json_encode($response['error']) : ($response['error'] ?? 'Error desconocido'),
                    ]);
                }
            }

            return $venta;
        });

        // Enviar notificación de pago
        if ($this->cliente_id) {
            $cliente = Customer::find($this->cliente_id);
            if ($cliente && $cliente->email) {
                app(\App\Services\EmailNotificationService::class)->sendPagoNotification(
                    $cliente->id,
                    $cliente->email,
                    new \App\Mail\PagoMail($venta)
                );
            }
        }

        session()->flash('mensaje', 'Venta procesada correctamente.');
        $this->redirect(route('caja.index'), navigate: true);
    }

    // Vaciar carrito
    public function vaciarCarrito(): void
    {
        $this->carrito = [];
        $this->recalcularTotales();
        $this->js('$flux.modal("confirmar-vaciar").close()');
    }

    public function confirmarEliminarDelCarrito(int $index)
    {
        $this->itemAEliminar = $index;
        $this->js('$flux.modal("confirmar-quitar").show()');
    }

    public function quitarProductoConfirmado()
    {
        if ($this->itemAEliminar !== null) {
            $this->eliminarDelCarrito($this->itemAEliminar);
            $this->itemAEliminar = null;
        }
        $this->js('$flux.modal("confirmar-quitar").close()');
    }

    public function render()
    {
        $this->calcularVuelto();
        
        // Productos disponibles para agregar
        $productos = Product::query()
            ->where('is_active', true)
            ->when(!$this->is_emergency, function($q) {
                $q->where(function ($sub) {
                    $sub->where('current_stock', '>', 0)
                        ->orWhere('type', 'SERVICIO');
                });
            })
            ->when($this->buscarProducto, function ($q, $buscarProducto) {
                $q->where(function ($sub) use ($buscarProducto) {
                    $sub->where('name', 'like', "%{$buscarProducto}%")
                        ->orWhere('codigo_barras', 'like', "%{$buscarProducto}%");
                });
            })
            ->when($this->filtroTipo, function ($q, $filtroTipo) {
                $q->where('type', $filtroTipo);
            })
            ->orderBy('name')
            ->limit(20)
            ->get();

        // Clientes para asignar comprobante
        $clientes = Customer::orderBy('first_name')->get();

        return view('livewire.caja.punto-venta', [
            'productos' => $productos,
            'clientes'  => $clientes,
        ]);
    }
}


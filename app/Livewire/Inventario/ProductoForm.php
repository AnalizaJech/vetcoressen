<?php

namespace App\Livewire\Inventario;

use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

// Formulario de producto/servicio
#[Layout('components.layouts.app')]
#[Title('Producto')]
class ProductoForm extends Component
{
    public ?int $productoId = null;
    public string $tipo = 'Accesorio';
    public string $principio_activo = '';
    public string $presentacion = '';
    public string $peso = '';
    public bool $requiere_receta = false;
    public string $categoria = '';
    public string $nombre = '';
    public string $codigo_barras = '';
    public string $precio_final = '';
    public string $tipo_afectacion_igv = 'Gravado';
    public bool $activo = true;
    public string $notas = '';

    protected function rules(): array
    {
        return [
            'tipo'               => 'required|in:Medicamento,Alimento,Accesorio,Servicio',
            'principio_activo'   => 'nullable|string|max:150',
            'presentacion'       => 'nullable|string|max:50',
            'peso'               => 'nullable|string|max:50',
            'requiere_receta'    => 'boolean',
            'categoria'          => 'nullable|string|max:100',
            'nombre'             => 'required|string|max:200',
            'codigo_barras'      => 'nullable|string|max:50',
            'precio_final'       => 'required|numeric|min:0',
            'tipo_afectacion_igv'=> 'required|in:Gravado,Inafecto,Exonerado',
            'notas'              => 'nullable|string|max:500',
        ];
    }

    public function generarCodigoBarras(): void
    {
        $this->codigo_barras = '20' . date('ymdHis') . mt_rand(0, 9);
    }

    public function mount(?int $id = null): void
    {
        if ($id) {
            $producto = Product::findOrFail($id);
            $this->productoId = $producto->id;
            
            // Convert uppercase from DB (e.g. 'MEDICAMENTO') to Title Case (e.g. 'Medicamento')
            $this->tipo = match(strtoupper($producto->type)) {
                'PRODUCTO', 'ACCESORIO' => 'Accesorio',
                'SERVICIO' => 'Servicio',
                'MEDICAMENTO' => 'Medicamento',
                'ALIMENTO' => 'Alimento',
                default => 'Accesorio'
            };
            
            $this->principio_activo = $producto->principio_activo ?? '';
            $this->presentacion = $producto->presentacion ?? '';
            $this->peso = $producto->weight ?? '';
            $this->requiere_receta = (bool) $producto->requiere_receta;
            $this->categoria = $producto->categoria ?? '';
            $this->nombre = $producto->name;
            $this->codigo_barras = $producto->codigo_barras ?? '';
            $this->precio_final = (string) ($producto->base_imponible ?? $producto->precio_final);
            $this->tipo_afectacion_igv = $producto->tipo_afectacion_igv ?? 'Gravado';
            $this->activo = (bool) $producto->is_active;
            $this->notas = $producto->notes ?? '';
        }
    }

    public function updatedTipo(): void
    {
        $this->nombre = '';
        $this->categoria = '';
        if ($this->tipo === 'Servicio') {
            $this->codigo_barras = '';
            $this->peso = '';
        }
    }

    public function guardar(): void
    {
        $this->validate();

        $pf = (float) $this->precio_final;
        if ($this->tipo_afectacion_igv === 'Gravado') {
            $base_imponible = $pf;
            $igv_monto = $pf * 0.18;
            $pf = $base_imponible + $igv_monto;
        } else {
            $base_imponible = $pf;
            $igv_monto = 0;
        }

        $datos = [
            'clinic_id'           => 1,
            'type'                => strtoupper($this->tipo),
            'principio_activo'    => $this->tipo === 'Medicamento' ? ($this->principio_activo ?: null) : null,
            'presentacion'        => $this->tipo === 'Medicamento' ? ($this->presentacion ?: null) : null,
            'weight'              => $this->tipo === 'Alimento' ? ($this->peso ?: null) : null,
            'requiere_receta'     => $this->tipo === 'Medicamento' ? $this->requiere_receta : false,
            'categoria'           => $this->categoria ?: null,
            'name'                => $this->nombre,
            'codigo_barras'       => $this->codigo_barras ?: null,
            'precio_final'        => $pf,
            'base_imponible'      => $base_imponible,
            'igv_monto'           => $igv_monto,
            'tipo_afectacion_igv' => $this->tipo_afectacion_igv,
            'margen_ganancia'     => null, // Calculado al ingresar lotes
            'minimum_stock'       => $this->tipo !== 'Servicio' ? 5 : 0,
            'is_active'           => $this->activo,
            'notes'               => $this->notas ?: null,
        ];

        if ($this->productoId) {
            Product::findOrFail($this->productoId)->update($datos);
            session()->flash('mensaje', 'alert.product_updated');
        } else {
            $datos['current_stock'] = 0;
            Product::create($datos);
            session()->flash('mensaje', 'alert.product_created');
        }

        $this->redirect(route('inventario.index'), navigate: true);
    }

    public function render()
    {
        $dbNombres = Product::where('type', $this->tipo)
            ->select('name')->distinct()->orderBy('name')->pluck('name')->toArray();
            
        $defaultNombres = match($this->tipo) {
            'Medicamento' => ['Amoxicilina', 'Bravecto', 'NexGard', 'Simparica', 'Paracetamol', 'Meloxicam', 'Tramadol', 'Doxiciclina', 'Cefalexina', 'Ivermectina', 'Prednisona', 'Clindamicina'],
            'Alimento' => ['Ricocan Adultos', 'Dog Chow', 'Cat Chow', 'Pro Plan', 'Royal Canin', 'Hills Prescription Diet', 'Eukanuba', 'Brit Care', 'Taste of the Wild', 'Felix', 'Whiskas'],
            'Accesorio' => ['Collar', 'Correa', 'Juguete Kong', 'Cama', 'Rascador', 'Plato', 'Bebedero', 'Arenero', 'Pechera', 'Cepillo', 'Cortauñas', 'Transportadora'],
            'Servicio' => ['Consulta General', 'Consulta Especializada', 'Corte de Pelo', 'Baño', 'Vacunación', 'Desparasitación', 'Hemograma', 'Radiografía', 'Ecografía', 'Hospitalización', 'Cirugía Menor', 'Limpieza Dental', 'Corte de Uñas', 'Profilaxis', 'Esterilización'],
            default => []
        };
        
        $nombresComunes = array_unique(array_merge($defaultNombres, $dbNombres));
        sort($nombresComunes);

        $allCategories = Category::where('type', $this->tipo)->orderBy('name')->pluck('name')->toArray();
        
        $defaultCategories = match($this->tipo) {
            'Medicamento' => ['Antibióticos', 'Antiinflamatorios', 'Antiparasitarios', 'Analgésicos', 'Vitaminas', 'Dermatológicos'],
            'Alimento' => ['Seco (Croquetas)', 'Húmedo (Latas/Sobres)', 'Prescripción Médica', 'Snacks y Premios', 'Leche Maternizada'],
            'Accesorio' => ['Collares y Correas', 'Juguetes', 'Camas y Mantas', 'Higiene y Belleza', 'Platos y Bebederos', 'Ropa'],
            'Servicio' => ['Consultas', 'Vacunación', 'Peluquería', 'Laboratorio', 'Cirugía'],
            default => []
        };
        
        $allCategories = array_unique(array_merge($defaultCategories, $allCategories));
        sort($allCategories);

        return view('livewire.inventario.producto-form', [
            'categorias' => collect($allCategories),
            'nombresComunes' => collect($nombresComunes)
        ]);
    }
}

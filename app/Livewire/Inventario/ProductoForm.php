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
        $dbNombres = Product::where('type', strtoupper($this->tipo))
            ->select('name')->distinct()->orderBy('name')->pluck('name')->toArray();
            
        $defaultNombres = match($this->tipo) {
            'Medicamento' => [
                'Amoxicillin + Clavulanate 250mg x 10 tab',
                'Amoxicillin + Clavulanate 500mg x 10 tab',
                'Amoxicillin 500mg x 100 tab',
                'Ampicillin 1g Injectable',
                'Apoquel (Oclacitinib) 3.6mg x 20 tab',
                'Apoquel (Oclacitinib) 5.4mg x 20 tab',
                'Apoquel (Oclacitinib) 16mg x 20 tab',
                'Azithromycin 200mg/5ml Suspension 30ml',
                'Benazepril 5mg x 28 tab',
                'Bravecto Chewable Dog 2-4.5kg',
                'Bravecto Chewable Dog 4.5-10kg',
                'Bravecto Chewable Dog 10-20kg',
                'Bravecto Chewable Dog 20-40kg',
                'Bravecto Chewable Dog 40-56kg',
                'Bravecto Plus Spot-On Cat 1.2-2.8kg',
                'Bravecto Plus Spot-On Cat 2.8-6.25kg',
                'Buprenorphine 0.3mg/ml Injectable 1ml',
                'Carprofen (Rimadyl) 25mg x 14 tab',
                'Carprofen (Rimadyl) 75mg x 14 tab',
                'Carprofen (Rimadyl) 100mg x 14 tab',
                'Cefalexin 250mg/5ml Suspension 60ml',
                'Cefalexin 500mg x 100 tab',
                'Cefovecin (Convenia) 80mg/ml 10ml',
                'Cerenia (Maropitant) 10mg/ml Injectable 20ml',
                'Cerenia (Maropitant) 16mg x 4 tab',
                'Cerenia (Maropitant) 24mg x 4 tab',
                'Cerenia (Maropitant) 60mg x 4 tab',
                'Chlorhexidine 4% Medicated Shampoo 250ml',
                'Clindamycin 150mg x 16 cap',
                'Clindamycin Oral Drops 25mg/ml 20ml',
                'Credelio (Lotilaner) 112mg Dog 2.5-5.5kg',
                'Credelio (Lotilaner) 450mg Dog 11-22kg',
                'Cytopoint (Lokivetmab) 10mg Injectable',
                'Cytopoint (Lokivetmab) 20mg Injectable',
                'Cytopoint (Lokivetmab) 30mg Injectable',
                'Denamarin SAMe Medium Dog 30 tab',
                'Dexamethasone 2mg/ml Injectable 50ml',
                'Doxycycline 100mg x 100 cap',
                'Drontal Plus Dog x 4 tab',
                'Drontal Cat x 2 tab',
                'Enalapril 5mg x 30 tab',
                'Enrofloxacin 50mg x 10 tab',
                'Enrofloxacin 150mg x 10 tab',
                'Enrofloxacin 5% Injectable 100ml',
                'Fenbendazole (Panacur) 500mg x 10 tab',
                'Fipronil Spot-On Dog 10-20kg',
                'Firocoxib (Previcox) 57mg x 10 tab',
                'Firocoxib (Previcox) 227mg x 10 tab',
                'Fluoxetine 10mg x 30 cap',
                'Frontline Plus Dog 10-20kg Pipette',
                'Furosemide 20mg x 20 tab',
                'Furosemide 40mg x 20 tab',
                'Gabapentin 100mg x 30 cap',
                'Gabapentin 300mg x 30 cap',
                'Ivermectin 1% Injectable 50ml',
                'Ketoprofen 10mg x 10 tab',
                'Levetiracetam (Keppra) 250mg x 30 tab',
                'Marbofloxacin 20mg x 10 tab',
                'Meloxicam 0.5mg/ml Oral Suspension 15ml',
                'Meloxicam 1.5mg/ml Oral Suspension 15ml',
                'Meloxicam 5mg/ml Injectable 20ml',
                'Metoclopramide 5mg/ml Injectable 10ml',
                'Metronidazole 250mg x 20 tab',
                'Metronidazole 500mg x 20 tab',
                'Milbemax Dog 5-25kg x 2 tab',
                'Milbemax Cat 2-8kg x 2 tab',
                'NexGard Spectra Dog 3.5-7.5kg',
                'NexGard Spectra Dog 7.5-15kg',
                'NexGard Spectra Dog 15-30kg',
                'NexGard Spectra Dog 30-60kg',
                'Nutri-Plus Gel High Energy 120g',
                'Omeprazole 10mg x 14 cap',
                'Omeprazole 20mg x 14 cap',
                'Ondansetron 4mg x 10 tab',
                'Optimmune (Cyclosporine 0.2%) Eye Ointment 3.5g',
                'Otomax Ear Ointment 15g',
                'Pet-Tinic Vitamin & Iron Supplement 118ml',
                'Phenobarbital 30mg x 30 tab',
                'Pimobendan (Vetmedin) 1.25mg x 50 tab',
                'Pimobendan (Vetmedin) 2.5mg x 50 tab',
                'Pimobendan (Vetmedin) 5mg x 50 tab',
                'Posatex Otic Drops 17.5g',
                'Praziquantel + Pyrantel Dewormer x 4 tab',
                'Prednisone 5mg x 20 tab',
                'Prednisolone 20mg x 20 tab',
                'Revolution Plus Cat 2.5-5kg Pipette',
                'Robenacoxib (Onsior) 6mg Cat x 6 tab',
                'Robenacoxib (Onsior) 20mg Dog x 7 tab',
                'Simparica Trio Dog 2.5-5kg',
                'Simparica Trio Dog 5-10kg',
                'Simparica Trio Dog 10-20kg',
                'Simparica Trio Dog 20-40kg',
                'Spironolactone 25mg x 30 tab',
                'Sucralfate 1g Oral Suspension 100ml',
                'Surolan Otic Suspension 15ml',
                'Tobramycin 0.3% Eye Drops 5ml',
                'Tramadol 50mg x 20 tab',
                'Trazodone 50mg x 30 tab',
                'Vetericyn Plus Wound & Skin Hydrogel 120ml'
            ],
            'Alimento' => [
                'Royal Canin Medium Adult 15kg',
                'Royal Canin Maxi Adult 15kg',
                'Royal Canin Mini Adult 7.5kg',
                'Royal Canin Feline Care Nutrition 4kg',
                'Royal Canin Feline Gastrointestinal 2kg',
                'Royal Canin Canine Renal 2kg',
                'Royal Canin Canine Hepatic 1.5kg',
                'Royal Canin Canine Urinary S/O 2kg',
                'Royal Canin Canine Hypoallergenic 2kg',
                'Hill\'s Science Diet Canine Adult 12kg',
                'Hill\'s Prescription Diet Canine k/d 3.85kg',
                'Hill\'s Prescription Diet Canine c/d 3.85kg',
                'Hill\'s Prescription Diet Canine i/d 3.85kg',
                'Hill\'s Prescription Diet Feline c/d 1.8kg',
                'Pro Plan Adult Dog Complete 15kg',
                'Pro Plan Feline Adult Optiderma 3kg',
                'Pro Plan Veterinary Diets EN Gastroenteric 2.7kg',
                'Taste of the Wild High Prairie Canine 12.2kg',
                'Taste of the Wild Canyon River Feline 6.6kg',
                'Purina ONE Feline Tender Selects 3.18kg',
                'Eukanuba Adult Medium Breed 15kg',
                'Brit Care Sensitive Venison & Potato 12kg',
                'Kitten Milk Replacer (KMR) Powder 340g',
                'Esbilac Puppy Milk Replacer Powder 340g',
                'Churu Puree Treats Cat Variety Pack'
            ],
            'Accesorio' => [
                'Adjustable Nylon Reflective Collar (M/L)',
                'Heavy Duty Dog Leash with Padded Handle (1.5m)',
                'No-Pull Dog Training Harness (Large)',
                'Orthopedic Memory Foam Dog Bed (XL)',
                'Cat Scratching Post Multi-Level Tree (120cm)',
                'Stainless Steel Non-Slip Food & Water Bowl Set',
                'Automatic Pet Water Fountain 2.5L',
                'Grooming Deshedding Undercoat Brush',
                'Ergonomic Pet Nail Trimmer with Safety Guard',
                'Airline Approved Soft-Sided Pet Travel Carrier',
                'Interactive Laser Toy for Cats',
                'Durable Rubber Chew Ball for Dogs',
                'Hygienic Cat Litter Box with Hood and Filter'
            ],
            'Servicio' => [
                'General Medical Consultation',
                'Specialized Cardiology Consultation',
                'Specialized Dermatology Consultation',
                'Emergency Triage & Consultation',
                'Grooming & Spa Full Care',
                'Medicinal Bath & Drying',
                'Puppy Complete Vaccination Protocol',
                'Annual Polyvalent (DHPPi+L) Booster Vaccine',
                'Rabies Vaccine Shot',
                'Feline Triple Vaccine (FPV, FHV, FCV)',
                'Internal & External Comprehensive Deworming',
                'Complete Blood Count (CBC) Panel',
                'Biochemical Blood Profile (12 Parameters)',
                'Digital Thoracic / Abdominal X-Ray',
                'Abdominal Diagnostic Ultrasound',
                'Ultrasonic Dental Cleaning & Polishing',
                'Canine / Feline Spay Surgery (Ovariohysterectomy)',
                'Canine / Feline Neuter Surgery (Castration)',
                'Minor Wound Suture & Debridement'
            ],
            default => []
        };
        
        $nombresComunes = array_unique(array_merge($defaultNombres, $dbNombres));
        sort($nombresComunes);

        $allCategories = Category::where('type', strtoupper($this->tipo))->orderBy('name')->pluck('name')->toArray();
        
        $defaultCategories = match($this->tipo) {
            'Medicamento' => [
                'Antibiotics & Antimicrobials',
                'Anti-inflammatories & Pain Relief (NSAIDs)',
                'Antiparasitics (Internal & External)',
                'Gastrointestinal & Hepatic Support',
                'Cardiovascular & Renal Care',
                'Dermatology, Shampoos & Topicals',
                'Ophthalmic & Otic Care',
                'Sedatives, Behavioral & Neurological',
                'Vitamins, Minerals & Supplements',
                'Vaccines & Biologicals'
            ],
            'Alimento' => [
                'Dry Food (Kibble)',
                'Wet Food (Cans / Pouches)',
                'Veterinary Prescription Diet',
                'Healthy Snacks & Dental Treats',
                'Puppy & Kitten Milk Replacers'
            ],
            'Accesorio' => [
                'Collars, Leashes & Harnesses',
                'Beds, Mats & Comfort',
                'Toys & Mental Stimulation',
                'Hygiene, Brushes & Grooming',
                'Bowls, Feeders & Fountains',
                'Carriers, Kennels & Travel'
            ],
            'Servicio' => [
                'General & Specialized Consultations',
                'Vaccinations & Preventative Care',
                'Laboratory & Clinical Diagnostics',
                'Imaging (X-Ray & Ultrasound)',
                'Grooming, Bath & Spa Care',
                'Surgeries & Clinical Procedures',
                'Dental Health & Prophylaxis'
            ],
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

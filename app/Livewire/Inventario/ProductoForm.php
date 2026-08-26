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
        $dbNombres = Product::where(function ($q) {
                if ($this->tipo === 'Medicamento') {
                    $q->whereIn(DB::raw('UPPER(type)'), ['MEDICAMENTO', 'PRODUCTO', 'MEDICINA', 'FARMACO'])
                      ->orWhere('categoria', 'like', '%medic%')
                      ->orWhere('categoria', 'like', '%farma%');
                } else {
                    $q->where(DB::raw('UPPER(type)'), strtoupper($this->tipo));
                }
            })
            ->select('name')->distinct()->orderBy('name')->pluck('name')->toArray();
            
        $defaultNombres = match($this->tipo) {
            'Medicamento' => [
                'Acepromazine 10mg Tablets',
                'Amikacin 250mg/ml Injectable',
                'Amoxicillin + Clavulanate 250mg Tablets',
                'Amoxicillin + Clavulanate 500mg Tablets',
                'Amoxicillin 500mg Tablets',
                'Ampicillin 1g Injectable',
                'Apoquel (Oclacitinib) 3.6mg Tablets',
                'Apoquel (Oclacitinib) 5.4mg Tablets',
                'Apoquel (Oclacitinib) 16mg Tablets',
                'Atipamezole (Antisedan) 5mg/ml Injectable',
                'Azithromycin 200mg/5ml Oral Suspension',
                'Benazepril 5mg Tablets',
                'Benazepril 20mg Tablets',
                'Betamethasone 2mg/ml Injectable',
                'Bravecto Chewable Tablet for Dogs',
                'Bravecto Plus Spot-On for Cats',
                'Bravecto Spot-On for Dogs',
                'Bromhexine 4mg/5ml Syrup',
                'Buprenorphine 0.3mg/ml Injectable',
                'Butorphanol 10mg/ml Injectable',
                'Carprofen (Rimadyl) 25mg Chewable Tablets',
                'Carprofen (Rimadyl) 75mg Chewable Tablets',
                'Carprofen (Rimadyl) 100mg Chewable Tablets',
                'Cefalexin 250mg/5ml Oral Suspension',
                'Cefalexin 500mg Tablets',
                'Cefazolin 1g Injectable',
                'Cefovecin (Convenia) 80mg/ml Injectable',
                'Cerenia (Maropitant) 10mg/ml Injectable',
                'Cerenia (Maropitant) 16mg Tablets',
                'Cerenia (Maropitant) 24mg Tablets',
                'Cerenia (Maropitant) 60mg Tablets',
                'Chlorhexidine 4% Medicated Shampoo',
                'Ciprofloxacin 0.3% Ophthalmic Drops',
                'Clavaseptin 250mg Palatable Tablets',
                'Clindamycin 150mg Capsules',
                'Clindamycin Oral Drops 25mg/ml',
                'Credelio (Lotilaner) Chewable Tablets for Dogs',
                'Credelio (Lotilaner) Chewable Tablets for Cats',
                'Cyclosporine (Atopica) 25mg Capsules',
                'Cyclosporine (Atopica) 50mg Capsules',
                'Cytopoint (Lokivetmab) 10mg Injectable',
                'Cytopoint (Lokivetmab) 20mg Injectable',
                'Cytopoint (Lokivetmab) 30mg Injectable',
                'Cytopoint (Lokivetmab) 40mg Injectable',
                'Denamarin SAMe Liver Health Tablets',
                'Dexamethasone 2mg/ml Injectable',
                'Diazepam 5mg/ml Injectable',
                'Dipyrone (Metamizole) 500mg/ml Injectable',
                'Doxycycline 100mg Capsules',
                'Doxycycline 200mg Tablets',
                'Drontal Plus Chewable Tablets for Dogs',
                'Drontal Puppy Oral Suspension',
                'Drontal Tablets for Cats',
                'Enalapril 5mg Tablets',
                'Enalapril 20mg Tablets',
                'Endogard Chewable Dewormer Tablets',
                'Enrofloxacin 50mg Tablets',
                'Enrofloxacin 150mg Tablets',
                'Enrofloxacin 5% Injectable',
                'Famotidine 20mg Tablets',
                'Fenbendazole (Panacur) 500mg Tablets',
                'Fenbendazole 10% Oral Suspension',
                'Fipronil Spot-On Pipettes for Dogs',
                'Fipronil Spot-On Pipettes for Cats',
                'Firocoxib (Previcox) 57mg Chewable Tablets',
                'Firocoxib (Previcox) 227mg Chewable Tablets',
                'Fluconazole 100mg Capsules',
                'Fluoxetine 10mg Capsules',
                'Frontline Plus Pipette for Dogs',
                'Frontline Plus Pipette for Cats',
                'Furosemide 20mg Tablets',
                'Furosemide 40mg Tablets',
                'Furosemide 50mg/ml Injectable',
                'Gabapentin 100mg Capsules',
                'Gabapentin 300mg Capsules',
                'Gentamicin 0.3% Eye Drops',
                'Hemolitan Pet Liquid Vitamin Supplement',
                'Hydroxyzine 25mg Tablets',
                'Itraconazole 100mg Capsules',
                'Ivermectin 1% Injectable',
                'Ketoconazole 200mg Tablets',
                'Ketoprofen 10mg Tablets',
                'Ketoprofen 1% Injectable',
                'Lactulose 66.7g/100ml Syrup',
                'Levetiracetam (Keppra) 250mg Tablets',
                'Levetiracetam (Keppra) 500mg Tablets',
                'Marbofloxacin 20mg Tablets',
                'Marbofloxacin 80mg Tablets',
                'Meloxicam 0.5mg/ml Oral Suspension for Cats',
                'Meloxicam 1.5mg/ml Oral Suspension for Dogs',
                'Meloxicam 5mg/ml Injectable',
                'Metoclopramide 5mg/ml Injectable',
                'Metronidazole 250mg Tablets',
                'Metronidazole 500mg Tablets',
                'Metronidazole 125mg/5ml Oral Suspension',
                'Milbemax Chewable Tablets for Dogs',
                'Milbemax Tablets for Cats',
                'Mirtazapine 15mg Tablets',
                'NexGard Chewable Tablets for Dogs',
                'NexGard Spectra Chewable Tablets for Dogs',
                'NexGard Combo Spot-On for Cats',
                'Nutri-Plus Gel High Energy Supplement',
                'Omeprazole 10mg Capsules',
                'Omeprazole 20mg Capsules',
                'Ondansetron 2mg/ml Injectable',
                'Ondansetron 4mg Tablets',
                'Optimmune (Cyclosporine 0.2%) Eye Ointment',
                'Otomax Ear Ointment',
                'Pet-Tinic Vitamin & Iron Supplement',
                'Phenobarbital 30mg Tablets',
                'Phenobarbital 100mg Tablets',
                'Pimobendan (Vetmedin) 1.25mg Chewable Tablets',
                'Pimobendan (Vetmedin) 2.5mg Chewable Tablets',
                'Pimobendan (Vetmedin) 5mg Chewable Tablets',
                'Posatex Otic Drops',
                'Potassium Bromide 250mg Capsules',
                'Praziquantel + Pyrantel Dewormer Tablets',
                'Prednisone 5mg Tablets',
                'Prednisone 20mg Tablets',
                'Prednisolone 5mg Tablets',
                'Prednisolone 20mg Tablets',
                'Pregabalin 50mg Capsules',
                'Pregabalin 75mg Capsules',
                'Profender Spot-On Dewormer for Cats',
                'Ranitidine 150mg Tablets',
                'Revolution Plus Spot-On Pipette for Cats',
                'Robenacoxib (Onsior) 6mg Tablets for Cats',
                'Robenacoxib (Onsior) 20mg Tablets for Dogs',
                'Robenacoxib (Onsior) 20mg/ml Injectable',
                'Semintra (Telmisartan) 4mg/ml Oral Solution',
                'Silymarin + SAMe Hepatic Support Tablets',
                'Simparica Chewable Tablets for Dogs',
                'Simparica Trio Chewable Tablets for Dogs',
                'Spironolactone 25mg Tablets',
                'Sucralfate 1g Oral Suspension',
                'Surolan Otic Suspension',
                'Sulfamethoxazole + Trimethoprim 480mg Tablets',
                'Tobramycin 0.3% Eye Drops',
                'Toltrazuril 5% Oral Suspension',
                'Torasemide 2mg Tablets',
                'Tramadol 50mg Tablets',
                'Tramadol 50mg/ml Injectable',
                'Trazodone 50mg Tablets',
                'Trazodone 100mg Tablets',
                'Ursodeoxycholic Acid 250mg Capsules',
                'Vetericyn Plus Wound & Skin Hydrogel',
                'Vitamin K1 (Phytomenadione) 10mg Tablets',
                'Xylazine 2% Injectable'
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

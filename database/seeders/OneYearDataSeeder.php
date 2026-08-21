<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/**
 * Production-grade massive seeder simulating 1 year of real VETCORESSEN clinic operations.
 * Generates coherent consultations, sales, inventory movements, medical records, and prescriptions in English.
 */
class OneYearDataSeeder extends Seeder
{
    private Carbon $startDate;
    private Carbon $endDate;
    private int $clinicId = 1;
    private array $vetIds = [];
    private int $adminId = 1;
    private array $medicationIds = [];

    public function run(): void
    {
        $this->startDate = Carbon::now()->subYear()->startOfMonth();
        $this->endDate   = Carbon::now()->addMonth()->endOfMonth();

        $this->command->info('🧹 Cleaning previous database records...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('prescriptions')->truncate();
        DB::table('medical_records')->truncate();
        DB::table('appointments')->truncate();
        DB::table('inventory_movements')->truncate();
        DB::table('product_batches')->truncate();
        DB::table('sale_details')->truncate();
        DB::table('sales')->truncate();
        DB::table('cash_registers')->truncate();
        DB::table('pets')->truncate();
        DB::table('customers')->truncate();
        DB::table('suppliers')->truncate();
        DB::table('products')->truncate();
        DB::table('appointment_reasons')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🏥 Starting full 1-year data simulation in English...');

        // 1. Detect or assign veterinarians
        $this->adminId = DB::table('users')->where('clinic_id', $this->clinicId)->min('id') ?? 1;
        
        $vetRoleId = DB::table('roles')->where('name', 'veterinario')->value('id');
        if ($vetRoleId) {
            $this->vetIds = DB::table('model_has_roles')
                ->where('role_id', $vetRoleId)
                ->where('model_type', 'App\\Models\\User')
                ->pluck('model_id')
                ->toArray();
        }

        if (count($this->vetIds) < 2) {
            $this->createSampleVets();
        }

        // Execute sub-seeders in transactional block for consistency
        DB::transaction(function () {
            $this->seedAppointmentReasons();
            $this->seedSuppliers();
            $this->seedProducts();
            $this->seedCustomersAndPets();
            $this->seedAppointmentsAndRecords();
            $this->seedInventoryAndSales();
        });

        $this->command->info('✅ Massive English data seeding completed successfully.');
    }

    private function createSampleVets(): void
    {
        $branch = DB::table('branches')->where('clinic_id', $this->clinicId)->first();
        $branchId = $branch ? $branch->id : null;

        $vets = [
            ['name' => 'Dr. Carlos', 'last_name' => 'Rodriguez Vargas',   'email' => 'carlos.vet@vetcoressen.com', 'phone' => '999111222', 'dni' => '87654321', 'cmvp' => 'CMVP-4521'],
            ['name' => 'Dr. Maria',  'last_name' => 'Lopez Mendoza',      'email' => 'maria.vet@vetcoressen.com',  'phone' => '999333444', 'dni' => '11223344', 'cmvp' => 'CMVP-3892'],
            ['name' => 'Dr. George', 'last_name' => 'Mendez Castillo',    'email' => 'george.vet@vetcoressen.com', 'phone' => '999555666', 'dni' => '55667788', 'cmvp' => 'CMVP-6103'],
        ];

        foreach ($vets as $v) {
            $existing = DB::table('users')->where('email', $v['email'])->first();
            if ($existing) {
                $this->vetIds[] = $existing->id;
                continue;
            }

            $userId = DB::table('users')->insertGetId([
                'clinic_id'        => $this->clinicId,
                'branch_id'        => $branchId,
                'name'             => $v['name'],
                'last_name'        => $v['last_name'],
                'email'            => $v['email'],
                'password'         => Hash::make('Vetcoressen2026!'),
                'phone'            => $v['phone'],
                'tipo_documento'   => 'DNI',
                'numero_documento' => $v['dni'],
                'cmvp'             => $v['cmvp'],
                'is_active'        => true,
                'email_verified_at'=> Carbon::now(),
                'created_at'       => $this->startDate,
                'updated_at'       => Carbon::now(),
            ]);

            $vetRoleId = DB::table('roles')->where('name', 'veterinario')->value('id');
            if ($vetRoleId) {
                DB::table('model_has_roles')->insert([
                    'role_id'    => $vetRoleId,
                    'model_type' => 'App\\Models\\User',
                    'model_id'   => $userId,
                ]);
            }

            $this->vetIds[] = $userId;
        }
    }

    private function seedAppointmentReasons(): void
    {
        $reasons = [
            ['name' => 'General Consultation',     'duration_minutes' => 30],
            ['name' => 'Vaccination',               'duration_minutes' => 20],
            ['name' => 'Deworming',                'duration_minutes' => 15],
            ['name' => 'Post-Op Checkup',          'duration_minutes' => 30],
            ['name' => 'Emergency',                'duration_minutes' => 60],
            ['name' => 'Scheduled Surgery',        'duration_minutes' => 120],
            ['name' => 'Bathing & Grooming',       'duration_minutes' => 60],
            ['name' => 'Ultrasound',               'duration_minutes' => 45],
            ['name' => 'X-Ray Radiography',        'duration_minutes' => 40],
            ['name' => 'Laboratory Analysis',      'duration_minutes' => 30],
            ['name' => 'Dermatology Exam',         'duration_minutes' => 40],
            ['name' => 'Dental Cleaning',          'duration_minutes' => 45],
            ['name' => 'Spay / Neuter',            'duration_minutes' => 90],
            ['name' => 'Weight Control',           'duration_minutes' => 20],
        ];

        foreach ($reasons as $r) {
            DB::table('appointment_reasons')->insertOrIgnore([
                'name'             => $r['name'],
                'duration_minutes' => $r['duration_minutes'],
                'is_active'        => true,
                'created_at'       => $this->startDate,
                'updated_at'       => Carbon::now(),
            ]);
        }
        $this->command->info('  ✔ Appointment reasons created in English.');
    }

    private function seedSuppliers(): void
    {
        $suppliers = [
            ['name' => 'Agrovet Market Global Inc.', 'ruc' => '20100134021', 'phone' => '+1-555-0192', 'email' => 'sales@agrovetmarket.com',  'contact_name' => 'Louis Parker',    'address' => '3198 Canada Ave, Lima'],
            ['name' => 'MSD Animal Health Global',   'ruc' => '20505672381', 'phone' => '+1-555-0188', 'email' => 'orders@msd-animal.com',    'contact_name' => 'Rose Mendoza',    'address' => '4600 East Parkway, Surco, Lima'],
            ['name' => 'Montana Veterinary Pharma',  'ruc' => '20100127912', 'phone' => '+1-555-0145', 'email' => 'supplies@montana-pharma.com','contact_name' => 'Frank Miller', 'address' => '341 Monterrey Rd, Lima'],
            ['name' => 'Royal Canin International',  'ruc' => '20512948761', 'phone' => '+1-555-0176', 'email' => 'distributors@royalcanin.com','contact_name' => 'Clara Vance',   'address' => '1050 Spring Blvd, Lima'],
            ['name' => 'Hill\'s Pet Nutrition Inc.', 'ruc' => '20501523837', 'phone' => '+1-555-0162', 'email' => 'hills.sales@colgate.com',   'contact_name' => 'David Turner',   'address' => '3535 Republic Way, San Isidro'],
        ];

        foreach ($suppliers as $s) {
            DB::table('suppliers')->insertOrIgnore(array_merge($s, [
                'clinic_id'  => $this->clinicId,
                'is_active'  => true,
                'created_at' => $this->startDate,
                'updated_at' => Carbon::now(),
            ]));
        }
        $this->command->info('  ✔ Suppliers created in English.');
    }

    private function seedProducts(): void
    {
        $products = [
            ['type' => 'MEDICAMENTO', 'cat' => 'Antibiotics',                        'name' => 'Amoxicillin 500mg x 100 tab',      'principio_activo' => 'Amoxicillin',         'presentacion' => 'Tablets',  'costo' => 35.00,  'precio' => 65.00,  'stock' => 120, 'min' => 20, 'requiere_receta' => true],
            ['type' => 'MEDICAMENTO', 'cat' => 'Antibiotics',                        'name' => 'Cephalexin 250mg/5ml Suspension',  'principio_activo' => 'Cephalexin',          'presentacion' => 'Bottle',   'costo' => 18.00,  'precio' => 38.00,  'stock' => 45,  'min' => 10, 'requiere_receta' => true],
            ['type' => 'MEDICAMENTO', 'cat' => 'Antibiotics',                        'name' => 'Enrofloxacin 50mg x 10 tab',       'principio_activo' => 'Enrofloxacin',       'presentacion' => 'Tablets',  'costo' => 12.00,  'precio' => 25.00,  'stock' => 80,  'min' => 15, 'requiere_receta' => true],
            ['type' => 'MEDICAMENTO', 'cat' => 'Antibiotics',                        'name' => 'Doxycycline 100mg x 100 cap',      'principio_activo' => 'Doxycycline',         'presentacion' => 'Capsules', 'costo' => 22.00,  'precio' => 45.00,  'stock' => 60,  'min' => 10, 'requiere_receta' => true],
            ['type' => 'MEDICAMENTO', 'cat' => 'Analgesics and Anti-inflammatories', 'name' => 'Meloxicam 1.5mg/ml Drops 15ml',    'principio_activo' => 'Meloxicam',           'presentacion' => 'Drops',    'costo' => 15.00,  'precio' => 32.00,  'stock' => 55,  'min' => 10, 'requiere_receta' => true],
            ['type' => 'MEDICAMENTO', 'cat' => 'Analgesics and Anti-inflammatories', 'name' => 'Carprofen 100mg x 14 tab',        'principio_activo' => 'Carprofen',           'presentacion' => 'Tablets',  'costo' => 45.00,  'precio' => 85.00,  'stock' => 30,  'min' => 5,  'requiere_receta' => true],
            ['type' => 'MEDICAMENTO', 'cat' => 'Internal Antiparasitics',            'name' => 'Praziquantel + Pyrantel x 4 tab',  'principio_activo' => 'Praziquantel/Pyrantel','presentacion' => 'Tablets',  'costo' => 6.00,   'precio' => 15.00,  'stock' => 200, 'min' => 40, 'requiere_receta' => false],
            ['type' => 'MEDICAMENTO', 'cat' => 'External Antiparasitics',            'name' => 'Fipronil Spot-On Dog 10-20kg',     'principio_activo' => 'Fipronil',            'presentacion' => 'Pipette',  'costo' => 8.00,   'precio' => 22.00,  'stock' => 150, 'min' => 30, 'requiere_receta' => false],
            ['type' => 'MEDICAMENTO', 'cat' => 'Vaccines',                           'name' => 'Canine DHPP Sextuple Vaccine',     'principio_activo' => 'Parvo/Distemper/Lepto','presentacion' => 'Vial',     'costo' => 18.00,  'precio' => 45.00,  'stock' => 80,  'min' => 15, 'requiere_receta' => false],
            ['type' => 'MEDICAMENTO', 'cat' => 'Vaccines',                           'name' => 'Rabies Vaccine Inactivated',       'principio_activo' => 'Inactivated Rabies',  'presentacion' => 'Vial',     'costo' => 10.00,  'precio' => 30.00,  'stock' => 100, 'min' => 20, 'requiere_receta' => false],
            ['type' => 'MEDICAMENTO', 'cat' => 'Vaccines',                           'name' => 'Feline Triple FVRCP Vaccine',       'principio_activo' => 'Rhinotracheitis/Calici/Panleuko','presentacion' => 'Vial','costo' => 22.00,'precio' => 55.00,  'stock' => 40,  'min' => 8,  'requiere_receta' => false],
            ['type' => 'ALIMENTO',    'cat' => 'Dry Food (Kibble)',                  'name' => 'Royal Canin Medium Adult 15kg',    'principio_activo' => null,                  'presentacion' => 'Bag',      'costo' => 185.00, 'precio' => 289.00, 'stock' => 12,  'min' => 3,  'requiere_receta' => false],
            ['type' => 'ALIMENTO',    'cat' => 'Dry Food (Kibble)',                  'name' => 'Hill\'s Science Diet Puppy 6.8kg', 'principio_activo' => null,                  'presentacion' => 'Bag',      'costo' => 125.00, 'precio' => 195.00, 'stock' => 8,   'min' => 2,  'requiere_receta' => false],
            ['type' => 'ACCESORIO',   'cat' => 'Toys',                               'name' => 'Kong Classic Medium Dog Toy',      'principio_activo' => null,                  'presentacion' => 'Unit',     'costo' => 28.00,  'precio' => 55.00,  'stock' => 12,  'min' => 3,  'requiere_receta' => false],
            ['type' => 'SERVICIO',    'cat' => 'Clinical Services',                  'name' => 'General Consultation',             'principio_activo' => null,                  'presentacion' => null,       'costo' => 0,      'precio' => 50.00,  'stock' => 0,   'min' => 0,  'requiere_receta' => false],
            ['type' => 'SERVICIO',    'cat' => 'Clinical Services',                  'name' => 'Emergency Consultation',           'principio_activo' => null,                  'presentacion' => null,       'costo' => 0,      'precio' => 120.00, 'stock' => 0,   'min' => 0,  'requiere_receta' => false],
            ['type' => 'SERVICIO',    'cat' => 'Clinical Services',                  'name' => 'Abdominal Ultrasound Scan',        'principio_activo' => null,                  'presentacion' => null,       'costo' => 0,      'precio' => 150.00, 'stock' => 0,   'min' => 0,  'requiere_receta' => false],
            ['type' => 'SERVICIO',    'cat' => 'Dog/Cat Grooming',                   'name' => 'Full Bath & Haircut Small Breed',  'principio_activo' => null,                  'presentacion' => null,       'costo' => 0,      'precio' => 45.00,  'stock' => 0,   'min' => 0,  'requiere_receta' => false],
        ];

        foreach ($products as $p) {
            $precioFinal = $p['precio'];
            $baseImponible = round($precioFinal / 1.18, 2);
            $igvMonto = round($precioFinal - $baseImponible, 2);

            $productId = DB::table('products')->insertGetId([
                'clinic_id'            => $this->clinicId,
                'type'                 => $p['type'],
                'categoria'            => $p['cat'],
                'principio_activo'     => $p['principio_activo'],
                'presentacion'         => $p['presentacion'],
                'name'                 => $p['name'],
                'precio_final'         => $precioFinal,
                'base_imponible'       => $baseImponible,
                'igv_monto'            => $igvMonto,
                'tipo_afectacion_igv'  => 'Gravado',
                'requiere_receta'      => $p['requiere_receta'],
                'current_stock'        => $p['stock'],
                'minimum_stock'        => $p['min'],
                'is_active'            => true,
                'created_at'           => $this->startDate,
                'updated_at'           => Carbon::now(),
            ]);

            if ($p['type'] === 'MEDICAMENTO') {
                $this->medicationIds[$p['name']] = $productId;
            }

            if ($p['type'] !== 'SERVICIO') {
                $daysToExpiry = match($p['name']) {
                    'Cephalexin 250mg/5ml Suspension' => 15,
                    'Meloxicam 1.5mg/ml Drops 15ml'   => 45,
                    'Enrofloxacin 50mg x 10 tab'      => -10,
                    default => rand(180, 540),
                };

                DB::table('product_batches')->insert([
                    'product_id'        => $productId,
                    'supplier_id'       => rand(1, 5),
                    'lote'              => 'LOT-' . rand(1000, 9999),
                    'fecha_vencimiento' => Carbon::now()->addDays($daysToExpiry)->format('Y-m-d'),
                    'costo_unitario'    => $p['costo'],
                    'precio_venta'      => $p['precio'],
                    'stock_inicial'     => $p['stock'] + 20,
                    'stock_actual'      => $p['stock'],
                    'created_at'        => $this->startDate,
                    'updated_at'        => Carbon::now(),
                ]);
            }
        }
        $this->command->info('  ✔ Products, services, and inventory batches created in English.');
    }

    private function seedCustomersAndPets(): void
    {
        $firstNames = ['John', 'Emily', 'Michael', 'Sarah', 'David', 'Jessica', 'Daniel', 'Ashley', 'James', 'Amanda', 'Robert', 'Jennifer', 'William', 'Elizabeth', 'Matthew', 'Megan'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Miller', 'Davis', 'Wilson', 'Anderson', 'Taylor', 'Thomas', 'Moore', 'Jackson', 'Martin', 'White', 'Harris'];
        $districts = ['La Molina', 'Surco', 'San Borja', 'Miraflores', 'San Isidro', 'Uptown District'];

        $speciesIds = DB::table('species')->pluck('id', 'name')->toArray();
        $canineId = $speciesIds['Canine'] ?? ($speciesIds['Canino'] ?? 1);
        $felineId = $speciesIds['Feline'] ?? ($speciesIds['Felino'] ?? 2);

        $breedsBySpecies = [];
        foreach ([$canineId, $felineId] as $spId) {
            $breedsBySpecies[$spId] = DB::table('breeds')->where('species_id', $spId)->pluck('id')->toArray();
        }

        // Create 200 clients (~320 pets)
        for ($i = 0; $i < 200; $i++) {
            $fn = $firstNames[array_rand($firstNames)];
            $ln = $lastNames[array_rand($lastNames)] . ' ' . $lastNames[array_rand($lastNames)];
            $district = $districts[array_rand($districts)];
            $dni = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            $createdAt = $this->startDate->copy()->addDays(rand(0, 300));

            $customerId = DB::table('customers')->insertGetId([
                'clinic_id'        => $this->clinicId,
                'tipo_documento'   => 'DNI',
                'numero_documento' => $dni,
                'first_name'       => $fn,
                'last_name'        => $ln,
                'email'            => strtolower($fn . '.' . explode(' ', $ln)[0] . rand(10, 99) . '@gmail.com'),
                'email_valido'     => true,
                'phone'            => '9' . rand(10000000, 99999999),
                'address'          => 'Avenue ' . rand(100, 999) . ', ' . $district,
                'country'          => 'Perú',
                'state'            => 'Lima',
                'city'             => $district,
                'is_active'        => true,
                'created_at'       => $createdAt,
                'updated_at'       => $createdAt,
            ]);

            $numPets = rand(1, 2);
            for ($p = 0; $p < $numPets; $p++) {
                $isDog = rand(1, 10) <= 7;
                $spId = $isDog ? $canineId : $felineId;
                $petNames = $isDog 
                    ? ['Max', 'Bella', 'Charlie', 'Luna', 'Cooper', 'Bailey', 'Daisy', 'Rocky', 'Lola', 'Buster', 'Teddy', 'Zoe']
                    : ['Milo', 'Oliver', 'Leo', 'Chloe', 'Sophie', 'Cleo', 'Kitty', 'Simba', 'Felix', 'Luna', 'Shadow', 'Jasper'];
                
                $breedIds = $breedsBySpecies[$spId] ?? [];
                $razaId = !empty($breedIds) ? $breedIds[array_rand($breedIds)] : null;

                DB::table('pets')->insert([
                    'clinic_id'     => $this->clinicId,
                    'customer_id'   => $customerId,
                    'name'          => $petNames[array_rand($petNames)],
                    'species_id'    => $spId,
                    'raza_id'       => $razaId,
                    'gender'        => rand(0, 1) ? 'M' : 'H',
                    'color'         => ['Brown', 'Black', 'White', 'Gray', 'Golden', 'Brindle'][array_rand(['Brown', 'Black', 'White', 'Gray', 'Golden', 'Brindle'])],
                    'birth_date'    => Carbon::now()->subMonths(rand(3, 120))->format('Y-m-d'),
                    'current_weight'=> $isDog ? rand(3, 35) : rand(2, 6),
                    'esterilizado'  => rand(0, 1),
                    'fallecido'     => false,
                    'created_at'    => $createdAt,
                    'updated_at'    => $createdAt,
                ]);
            }
        }
        $this->command->info('  ✔ 200 Customers and ~320 Pets created with English names.');
    }

    private function seedAppointmentsAndRecords(): void
    {
        $pets = DB::table('pets')->select('id', 'customer_id', 'species_id', 'current_weight')->get();
        $reasonIds = DB::table('appointment_reasons')->pluck('id', 'name')->toArray();

        // Clinical data mapped seasonally in English
        $clinicalData = [
            'Summer' => [
                ['disease' => 'Flea Allergy Dermatitis',       'symptom' => 'Excessive pruritus, skin erythema and constant scratching'],
                ['disease' => 'Flea Allergy Dermatitis',       'symptom' => 'Hair loss in lumbar area and severe itching'],
                ['disease' => 'Bilateral External Otitis',     'symptom' => 'Frequent head shaking, ear canal inflammation and odor'],
                ['disease' => 'Acute Gastroenteritis',         'symptom' => 'Frequent vomiting, mild dehydration and lethargy'],
                ['disease' => 'Healthy patient - wellness',    'symptom' => 'Routine preventive wellness check and deworming'],
            ],
            'Winter' => [
                ['disease' => 'Kennel Cough (Tracheobronchitis)', 'symptom' => 'Persistent dry cough, mild fever and clear nasal discharge'],
                ['disease' => 'Feline Viral Rhinotracheitis',     'symptom' => 'Frequent sneezing, conjunctivitis and ocular discharge'],
                ['disease' => 'Acute Gastroenteritis',           'symptom' => 'Watery diarrhea, inappetence and abdominal discomfort'],
                ['disease' => 'Bilateral External Otitis',       'symptom' => 'Ear scratching, head tilt and cerumen buildup'],
                ['disease' => 'Healthy patient - wellness',      'symptom' => 'Annual booster vaccination and general evaluation'],
            ],
            'Transition' => [
                ['disease' => 'Canine Ehrlichiosis',           'symptom' => 'High fever, lethargy, pale mucous membranes and anorexia'],
                ['disease' => 'Bilateral External Otitis',     'symptom' => 'Ear sensitivity, odor and mild discharge'],
                ['disease' => 'Flea Allergy Dermatitis',       'symptom' => 'Lumbar alopecia and severe flea bite irritation'],
                ['disease' => 'Acute Gastroenteritis',         'symptom' => 'Soft stools, vomiting and decreased appetite'],
                ['disease' => 'Healthy patient - wellness',    'symptom' => 'Annual health review, dental check and weight control'],
            ]
        ];

        $appointmentIdCounter = 1;
        $totalMonths = (int)$this->startDate->diffInMonths($this->endDate) + 1;

        for ($month = 0; $month < $totalMonths; $month++) {
            $monthStart = $this->startDate->copy()->addMonths($month);
            $monthEnd = $monthStart->copy()->endOfMonth();
            if ($monthEnd->isAfter($this->endDate)) $monthEnd = $this->endDate->copy();

            $monthNumber = $monthStart->month;
            if (in_array($monthNumber, [12, 1, 2, 3])) {
                $season = 'Summer';
            } elseif (in_array($monthNumber, [6, 7, 8, 9])) {
                $season = 'Winter';
            } else {
                $season = 'Transition';
            }

            // ~150 appointments per month
            for ($c = 0; $c < 150; $c++) {
                $pet = $pets->random();
                $apptDate = $monthStart->copy()->addDays(rand(0, 27))->setTime(rand(8, 17), rand(0, 3) * 15, 0);

                if ($apptDate->isFuture()) {
                    $status = rand(0, 1) ? 'CONFIRMADA' : 'PENDIENTE';
                } else {
                    $randVal = rand(1, 100);
                    $status = $randVal <= 85 ? 'COMPLETADA' : ($randVal <= 95 ? 'CANCELADA' : 'EXCEDIDO');
                }

                $clinicalCase = $clinicalData[$season][array_rand($clinicalData[$season])];
                $reasonName = str_contains($clinicalCase['disease'], 'wellness') ? 'Vaccination' : 'General Consultation';
                $reasonId = $reasonIds[$reasonName] ?? 1;
                $vetId = $this->vetIds[array_rand($this->vetIds)];

                // Insert Appointment
                DB::table('appointments')->insert([
                    'id'                    => $appointmentIdCounter,
                    'clinic_id'             => $this->clinicId,
                    'customer_id'           => $pet->customer_id,
                    'pet_id'                => $pet->id,
                    'veterinarian_id'       => $vetId,
                    'fecha_hora'            => $apptDate,
                    'end_time'              => $apptDate->copy()->addMinutes(30)->format('H:i:s'),
                    'reason'                => $clinicalCase['symptom'],
                    'appointment_reason_id' => $reasonId,
                    'status'                => $status,
                    'notes'                 => 'Scheduled via clinical appointment system.',
                    'created_at'            => $apptDate->copy()->subDays(rand(1, 5)),
                    'updated_at'            => $apptDate,
                ]);

                // If completed, create Medical Record & Prescription
                if ($status === 'COMPLETADA') {
                    $recordId = DB::table('medical_records')->insertGetId([
                        'clinic_id'                => $this->clinicId,
                        'pet_id'                   => $pet->id,
                        'veterinarian_id'          => $vetId,
                        'appointment_id'           => $appointmentIdCounter,
                        'date'                     => $apptDate,
                        'reason'                   => $clinicalCase['symptom'],
                        'weight'                   => round($pet->current_weight + (rand(-10, 10) / 10), 2),
                        'temperature'              => round(rand(378, 395) / 10, 1),
                        'heart_rate'               => $pet->species_id == 2 ? rand(120, 200) : rand(70, 140),
                        'respiratory_rate'         => rand(15, 35),
                        'anamnesis'                => 'Patient presented for general clinical triage. ' . $clinicalCase['symptom'],
                        'diagnostico_presuntivo'   => $clinicalCase['disease'],
                        'tratamiento_indicaciones' => 'Supportive and symptomatic treatment according to veterinary clinical protocols.',
                        'created_at'               => $apptDate,
                        'updated_at'               => $apptDate,
                    ]);

                    if (!str_contains($clinicalCase['disease'], 'wellness')) {
                        $this->seedPrescriptionForRecord($recordId, $clinicalCase['disease'], $apptDate);
                    }
                }

                $appointmentIdCounter++;
            }
        }
        $this->command->info('  ✔ Appointments, Medical Records, and Prescriptions created in English.');
    }

    private function seedPrescriptionForRecord(int $recordId, string $disease, Carbon $date): void
    {
        $prescriptionData = match ($disease) {
            'Flea Allergy Dermatitis' => [
                'name' => 'Fipronil Spot-On Dog 10-20kg',
                'dosage' => '1 pipette',
                'frequency' => 'Every 30 days',
                'duration' => '1 application',
                'via' => 'TÓPICA',
                'days' => 1,
                'qty' => 1,
                'indicaciones' => 'Apply directly to the skin at the base of the neck.'
            ],
            'Bilateral External Otitis' => [
                'name' => 'Meloxicam 1.5mg/ml Drops 15ml',
                'dosage' => '5 drops',
                'frequency' => 'Every 24 hours',
                'duration' => '5 days',
                'via' => 'ORAL',
                'days' => 5,
                'qty' => 1,
                'indicaciones' => 'Administer with food. Do not exceed recommended dosage.'
            ],
            'Acute Gastroenteritis' => [
                'name' => 'Cephalexin 250mg/5ml Suspension',
                'dosage' => '2.5 ml',
                'frequency' => 'Every 12 hours',
                'duration' => '7 days',
                'via' => 'ORAL',
                'days' => 7,
                'qty' => 1,
                'indicaciones' => 'Shake well before use. Keep refrigerated.'
            ],
            'Kennel Cough (Tracheobronchitis)', 'Canine Ehrlichiosis' => [
                'name' => 'Doxycycline 100mg x 100 cap',
                'dosage' => '1/2 tablet',
                'frequency' => 'Every 12 hours',
                'duration' => '10 days',
                'via' => 'ORAL',
                'days' => 10,
                'qty' => 10,
                'indicaciones' => 'Give after meals. Complete the full antibiotic course.'
            ],
            'Feline Viral Rhinotracheitis' => [
                'name' => 'Amoxicillin 500mg x 100 tab',
                'dosage' => '1/4 tablet',
                'frequency' => 'Every 12 hours',
                'duration' => '7 days',
                'via' => 'ORAL',
                'days' => 7,
                'qty' => 4,
                'indicaciones' => 'May be dissolved in water if needed to facilitate intake.'
            ],
            default => null
        };

        if ($prescriptionData) {
            $prodId = $this->medicationIds[$prescriptionData['name']] ?? null;
            DB::table('prescriptions')->insert([
                'clinic_id'           => $this->clinicId,
                'medical_record_id'   => $recordId,
                'product_id'          => $prodId,
                'medicamento'         => $prescriptionData['name'],
                'dosage'              => $prescriptionData['dosage'],
                'frequency'           => $prescriptionData['frequency'],
                'duration'            => $prescriptionData['duration'],
                'via_administracion'  => $prescriptionData['via'],
                'duracion_dias'       => $prescriptionData['days'],
                'indicaciones'        => $prescriptionData['indicaciones'],
                'cantidad_dispensada' => $prescriptionData['qty'],
                'dispensado'          => (bool)rand(0, 1),
                'created_at'          => $date,
                'updated_at'          => $date,
            ]);
        }
    }

    private function seedInventoryAndSales(): void
    {
        $vetsAndAdmin = array_merge($this->vetIds, [$this->adminId]);
        $customers = DB::table('customers')->pluck('id')->toArray();
        $products = DB::table('products')->where('clinic_id', $this->clinicId)->get();

        $totalMonths = (int)$this->startDate->diffInMonths($this->endDate) + 1;

        // 1. Monthly cash registers
        for ($month = 0; $month < $totalMonths; $month++) {
            $monthStart = $this->startDate->copy()->addMonths($month);
            $openedAt = $monthStart->copy()->setTime(8, 0, 0);
            $closedAt = $monthStart->copy()->endOfMonth()->setTime(20, 0, 0);

            DB::table('cash_registers')->insert([
                'user_id'           => $this->adminId,
                'opened_at'         => $openedAt,
                'closed_at'         => $closedAt,
                'opening_amount'    => 200.00,
                'calculated_amount' => 18500.00,
                'real_amount'       => 18500.00,
                'difference'        => 0.00,
                'status'            => 'CERRADA',
                'created_at'        => $openedAt,
                'updated_at'        => $closedAt,
            ]);
        }

        // 2. 1800 sales
        $saleIdCounter = 1;
        $totalDays = (int)$this->startDate->diffInDays($this->endDate);

        for ($s = 0; $s < 1800; $s++) {
            $saleDate = $this->startDate->copy()->addDays(rand(0, $totalDays))->setTime(rand(9, 19), rand(0, 59), 0);
            $customerId = $customers[array_rand($customers)];
            $cajeroId = $vetsAndAdmin[array_rand($vetsAndAdmin)];

            $selectedProducts = $products->random(rand(1, 3));
            $subtotalVenta = 0;
            $igvVenta = 0;
            $totalVenta = 0;
            $details = [];

            foreach ($selectedProducts as $prod) {
                $qty = rand(1, 2);
                $precioUnitario = $prod->precio_final;
                $baseItem = round($precioUnitario / 1.18 * $qty, 2);
                $igvItem = round($precioUnitario * $qty - $baseItem, 2);
                $subtotalItem = round($precioUnitario * $qty, 2);

                $details[] = [
                    'sale_id'               => $saleIdCounter,
                    'product_id'            => $prod->id,
                    'description'           => $prod->name,
                    'quantity'              => $qty,
                    'base_imponible'        => $baseItem,
                    'igv_monto'             => $igvItem,
                    'precio_final_unitario' => $precioUnitario,
                    'subtotal'              => $subtotalItem,
                    'created_at'            => $saleDate,
                    'updated_at'            => $saleDate,
                ];

                $subtotalVenta += $baseItem;
                $igvVenta += $igvItem;
                $totalVenta += $subtotalItem;

                if ($prod->type !== 'SERVICIO') {
                    DB::table('inventory_movements')->insert([
                        'clinic_id'          => $this->clinicId,
                        'product_id'         => $prod->id,
                        'user_id'            => $cajeroId,
                        'type'               => 'SALIDA_VENTA',
                        'quantity'           => -$qty,
                        'costo_unitario'     => round($prod->precio_final / 1.18 * 0.6, 2),
                        'stock_anterior'     => $prod->current_stock,
                        'stock_posterior'    => max(0, $prod->current_stock - $qty),
                        'referencia_tipo'    => 'App\\Models\\Sale',
                        'referencia_id'      => $saleIdCounter,
                        'created_at'         => $saleDate,
                        'updated_at'         => $saleDate,
                    ]);
                }
            }

            DB::table('sales')->insert([
                'id'               => $saleIdCounter,
                'clinic_id'        => $this->clinicId,
                'customer_id'      => $customerId,
                'cajero_id'        => $cajeroId,
                'tipo_comprobante' => ['NOTA_VENTA', 'BOLETA', 'FACTURA'][array_rand(['NOTA_VENTA', 'BOLETA', 'FACTURA'])],
                'subtotal'         => round($subtotalVenta, 2),
                'igv'              => round($igvVenta, 2),
                'total'            => round($totalVenta, 2),
                'payment_method'   => ['EFECTIVO', 'TARJETA', 'YAPE_PLIN', 'TRANSFERENCIA'][array_rand(['EFECTIVO', 'TARJETA', 'YAPE_PLIN', 'TRANSFERENCIA'])],
                'status'           => 'PAGADO',
                'created_at'       => $saleDate,
                'updated_at'       => $saleDate,
            ]);

            DB::table('sale_details')->insert($details);

            $saleIdCounter++;
        }
        $this->command->info('  ✔ 1800 Sales and inventory movements seeded successfully.');
    }
}

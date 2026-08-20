<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/**
 * Seeder masivo de producción que simula 1 año de operación real de VETCORESSEN.
 * Genera datos coherentes para consultas, ventas, inventario e historias clínicas.
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
        $this->endDate   = Carbon::now()->addMonth()->endOfMonth(); // Hasta el mes que viene (Septiembre 2026)

        $this->command->info('🧹 Limpiando tablas de datos previos...');
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
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🏥 Iniciando simulación de datos para 1 año y medio de uso real...');

        // 1. Detectar o crear veterinarios
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

        // Ejecutar los sub-seeders dentro de una transacción para velocidad y consistencia
        DB::transaction(function () {
            $this->seedAppointmentReasons();
            $this->seedSuppliers();
            $this->seedProducts();
            $this->seedCustomersAndPets();
            $this->seedAppointmentsAndRecords();
            $this->seedInventoryAndSales();
        });

        $this->command->info('✅ Carga masiva completada con éxito.');
    }

    private function createSampleVets(): void
    {
        $branch = DB::table('branches')->where('clinic_id', $this->clinicId)->first();
        $branchId = $branch ? $branch->id : null;

        $vets = [
            ['name' => 'Dr. Carlos', 'last_name' => 'Rodríguez Vargas',   'email' => 'carlos.vet@vetcoressen.pe', 'phone' => '999111222', 'dni' => '87654321', 'cmvp' => 'CMVP-4521'],
            ['name' => 'Dra. María', 'last_name' => 'López Mendoza',      'email' => 'maria.vet@vetcoressen.pe',  'phone' => '999333444', 'dni' => '11223344', 'cmvp' => 'CMVP-3892'],
            ['name' => 'Dr. Jorge',  'last_name' => 'Méndez Castillo',    'email' => 'jorge.vet@vetcoressen.pe',  'phone' => '999555666', 'dni' => '55667788', 'cmvp' => 'CMVP-6103'],
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
            ['name' => 'Consulta General',          'duration_minutes' => 30],
            ['name' => 'Vacunación',                'duration_minutes' => 20],
            ['name' => 'Desparasitación',           'duration_minutes' => 15],
            ['name' => 'Control Post-Operatorio',   'duration_minutes' => 30],
            ['name' => 'Emergencia',                'duration_minutes' => 60],
            ['name' => 'Cirugía Programada',        'duration_minutes' => 120],
            ['name' => 'Baño y Grooming',           'duration_minutes' => 60],
            ['name' => 'Ecografía',                 'duration_minutes' => 45],
            ['name' => 'Radiografía',               'duration_minutes' => 40],
            ['name' => 'Análisis de Laboratorio',   'duration_minutes' => 30],
            ['name' => 'Dermatología',              'duration_minutes' => 40],
            ['name' => 'Odontología',               'duration_minutes' => 45],
            ['name' => 'Esterilización',            'duration_minutes' => 90],
            ['name' => 'Control de Peso',           'duration_minutes' => 20],
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
        $this->command->info('  ✔ Motivos de cita creados.');
    }

    private function seedSuppliers(): void
    {
        $suppliers = [
            ['name' => 'Agrovet Market S.A.',       'ruc' => '20100134021', 'phone' => '01-6133535', 'email' => 'ventas@agrovetmarket.com',  'contact_name' => 'Luis Paredes',    'address' => 'Av. Canadá 3198, San Luis, Lima'],
            ['name' => 'MSD Animal Health Perú',    'ruc' => '20505672381', 'phone' => '01-7108800', 'email' => 'pedidos@msd-animal.com.pe', 'contact_name' => 'Rosa Mendoza',    'address' => 'Av. Javier Prado Este 4600, Surco, Lima'],
            ['name' => 'Droguería Montana S.A.',    'ruc' => '20100127912', 'phone' => '01-2173500', 'email' => 'ventas@montana.com.pe',     'contact_name' => 'Fernando Quispe', 'address' => 'Jr. Monterrey 341, Chacarilla, Lima'],
            ['name' => 'Royal Canin Perú',          'ruc' => '20512948761', 'phone' => '01-7156200', 'email' => 'distribuidores@royalcanin.com', 'contact_name' => 'Carla Vega',  'address' => 'Av. Primavera 1050, Surco, Lima'],
            ['name' => 'Hill\'s Pet Nutrition',     'ruc' => '20501523837', 'phone' => '01-6280500', 'email' => 'hillsperu@colgate.com',     'contact_name' => 'Diego Torres',    'address' => 'Av. República de Panamá 3535, San Isidro'],
        ];

        foreach ($suppliers as $s) {
            DB::table('suppliers')->insertOrIgnore(array_merge($s, [
                'clinic_id'  => $this->clinicId,
                'is_active'  => true,
                'created_at' => $this->startDate,
                'updated_at' => Carbon::now(),
            ]));
        }
        $this->command->info('  ✔ Proveedores creados.');
    }

    private function seedProducts(): void
    {
        $cats = DB::table('categories')->pluck('id', 'name')->toArray();

        $products = [
            ['type' => 'MEDICAMENTO', 'cat' => 'Antibióticos',     'name' => 'Amoxicilina 500mg x 100 tab',        'principio_activo' => 'Amoxicilina',        'presentacion' => 'Tableta', 'costo' => 35.00,  'precio' => 65.00,  'stock' => 120, 'min' => 20, 'requiere_receta' => true],
            ['type' => 'MEDICAMENTO', 'cat' => 'Antibióticos',     'name' => 'Cefalexina 250mg/5ml Suspensión',    'principio_activo' => 'Cefalexina',         'presentacion' => 'Frasco',  'costo' => 18.00,  'precio' => 38.00,  'stock' => 45,  'min' => 10, 'requiere_receta' => true],
            ['type' => 'MEDICAMENTO', 'cat' => 'Antibióticos',     'name' => 'Enrofloxacina 50mg x 10 tab',        'principio_activo' => 'Enrofloxacina',      'presentacion' => 'Tableta', 'costo' => 12.00,  'precio' => 25.00,  'stock' => 80,  'min' => 15, 'requiere_receta' => true],
            ['type' => 'MEDICAMENTO', 'cat' => 'Antibióticos',     'name' => 'Doxiciclina 100mg x 100 cap',        'principio_activo' => 'Doxiciclina',        'presentacion' => 'Cápsula', 'costo' => 22.00,  'precio' => 45.00,  'stock' => 60,  'min' => 10, 'requiere_receta' => true],
            ['type' => 'MEDICAMENTO', 'cat' => 'Analgésicos y Antiinflamatorios', 'name' => 'Meloxicam 1.5mg/ml Gotas 15ml',  'principio_activo' => 'Meloxicam',  'presentacion' => 'Gotas',   'costo' => 15.00,  'precio' => 32.00,  'stock' => 55,  'min' => 10, 'requiere_receta' => true],
            ['type' => 'MEDICAMENTO', 'cat' => 'Analgésicos y Antiinflamatorios', 'name' => 'Carprofeno 100mg x 14 tab',      'principio_activo' => 'Carprofeno', 'presentacion' => 'Tableta', 'costo' => 45.00,  'precio' => 85.00,  'stock' => 30,  'min' => 5,  'requiere_receta' => true],
            ['type' => 'MEDICAMENTO', 'cat' => 'Antiparasitarios Internos', 'name' => 'Praziquantel + Pirantel x 4 tab',   'principio_activo' => 'Praziquantel/Pirantel', 'presentacion' => 'Tableta',   'costo' => 6.00,  'precio' => 15.00, 'stock' => 200, 'min' => 40, 'requiere_receta' => false],
            ['type' => 'MEDICAMENTO', 'cat' => 'Antiparasitarios Externos', 'name' => 'Fipronil Pipeta Perro 10-20kg',     'principio_activo' => 'Fipronil',              'presentacion' => 'Pipeta',    'costo' => 8.00,  'precio' => 22.00, 'stock' => 150, 'min' => 30, 'requiere_receta' => false],
            ['type' => 'MEDICAMENTO', 'cat' => 'Vacunas', 'name' => 'Vacuna Séxtuple Canina',           'principio_activo' => 'Parvovirus/Moquillo/Leptospira', 'presentacion' => 'Dosis',  'costo' => 18.00,  'precio' => 45.00,  'stock' => 80,  'min' => 15, 'requiere_receta' => false],
            ['type' => 'MEDICAMENTO', 'cat' => 'Vacunas', 'name' => 'Vacuna Antirrábica',               'principio_activo' => 'Virus Rábico Inactivado',                               'presentacion' => 'Dosis',  'costo' => 10.00,  'precio' => 30.00,  'stock' => 100, 'min' => 20, 'requiere_receta' => false],
            ['type' => 'MEDICAMENTO', 'cat' => 'Vacunas', 'name' => 'Vacuna Triple Felina',             'principio_activo' => 'Rinotraqueitis/Calicivirus/Panleucopenia',               'presentacion' => 'Dosis',  'costo' => 22.00,  'precio' => 55.00,  'stock' => 40,  'min' => 8,  'requiere_receta' => false],
            ['type' => 'ALIMENTO', 'cat' => 'Alimento Seco (Croquetas)', 'name' => 'Royal Canin Medium Adult 15kg',     'principio_activo' => null, 'presentacion' => 'Saco',   'costo' => 185.00, 'precio' => 289.00, 'stock' => 12, 'min' => 3, 'requiere_receta' => false],
            ['type' => 'ALIMENTO', 'cat' => 'Alimento Seco (Croquetas)', 'name' => 'Hill\'s Science Diet Puppy 6.8kg',  'principio_activo' => null, 'presentacion' => 'Saco',   'costo' => 125.00, 'precio' => 195.00, 'stock' => 8,  'min' => 2, 'requiere_receta' => false],
            ['type' => 'ACCESORIO', 'cat' => 'Juguetes', 'name' => 'Kong Classic Mediano',                                 'principio_activo' => null, 'presentacion' => 'Unidad', 'costo' => 28.00, 'precio' => 55.00,  'stock' => 12, 'min' => 3, 'requiere_receta' => false],
            ['type' => 'SERVICIO', 'cat' => 'Servicios Clínicos',       'name' => 'Consulta General',              'principio_activo' => null, 'presentacion' => null, 'costo' => 0,     'precio' => 50.00,  'stock' => 0, 'min' => 0, 'requiere_receta' => false],
            ['type' => 'SERVICIO', 'cat' => 'Servicios Clínicos',       'name' => 'Consulta de Emergencia',        'principio_activo' => null, 'presentacion' => null, 'costo' => 0,     'precio' => 120.00, 'stock' => 0, 'min' => 0, 'requiere_receta' => false],
            ['type' => 'SERVICIO', 'cat' => 'Servicios Clínicos',       'name' => 'Ecografía Abdominal',           'principio_activo' => null, 'presentacion' => null, 'costo' => 0,     'precio' => 150.00, 'stock' => 0, 'min' => 0, 'requiere_receta' => false],
            ['type' => 'SERVICIO', 'cat' => 'Peluquería Canina/Felina', 'name' => 'Baño y Corte Completo Raza Pequeña',           'principio_activo' => null, 'presentacion' => null, 'costo' => 0, 'precio' => 45.00,  'stock' => 0, 'min' => 0, 'requiere_receta' => false],
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
                // Generar fecha de expiración realista
                $daysToExpiry = match($p['name']) {
                    'Cefalexina 250mg/5ml Suspensión' => 15,  // Expira en 15 días (alerta crítica)
                    'Meloxicam 1.5mg/ml Gotas 15ml' => 45,    // Expira en 45 días (alerta)
                    'Enrofloxacina 50mg x 10 tab' => -10,     // Expirado hace 10 días
                    default => rand(180, 540),                // Futuro lejano
                };

                DB::table('product_batches')->insert([
                    'product_id' => $productId,
                    'supplier_id' => rand(1, 5),
                    'lote' => 'LOT-' . rand(1000, 9999),
                    'fecha_vencimiento' => Carbon::now()->addDays($daysToExpiry)->format('Y-m-d'),
                    'costo_unitario' => $p['costo'],
                    'precio_venta' => $p['precio'],
                    'stock_inicial' => $p['stock'] + 20,
                    'stock_actual' => $p['stock'],
                    'created_at' => $this->startDate,
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
        $this->command->info('  ✔ Productos/Servicios base y Lotes de Inventario creados.');
    }

    private function seedCustomersAndPets(): void
    {
        $firstNames = ['Juan', 'Pedro', 'María', 'Luis', 'Ana', 'Carlos', 'Sofía', 'Diego', 'Carmen', 'Jorge', 'Patricia', 'Fernando', 'Lucía', 'Roberto', 'Andrea'];
        $lastNames = ['Quispe', 'Flores', 'Sánchez', 'García', 'Rodríguez', 'Rojas', 'Huamán', 'Mendoza', 'Torres', 'López', 'Espinoza', 'Chávez', 'Vargas'];
        $districts = ['La Molina', 'Santiago de Surco', 'San Borja', 'Miraflores', 'San Isidro', 'Jesús María'];

        $speciesIds = DB::table('species')->pluck('id', 'name')->toArray();
        $breedsBySpecies = [];
        foreach ($speciesIds as $spName => $spId) {
            $breedsBySpecies[$spId] = DB::table('breeds')->where('species_id', $spId)->pluck('id')->toArray();
        }

        // Crear 200 clientes (Total ~320 mascotas)
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
                'address'          => 'Av. Principal ' . rand(100, 999) . ', ' . $district,
                'country'          => 'Perú',
                'state'            => 'Lima',
                'city'             => $district,
                'is_active'        => true,
                'created_at'       => $createdAt,
                'updated_at'       => $createdAt,
            ]);

            // Generar 1-2 mascotas por cliente (Total ~320)
            $numPets = rand(1, 2);
            for ($p = 0; $p < $numPets; $p++) {
                $isDog = rand(1, 10) <= 7;
                $spId = $isDog ? ($speciesIds['Canino'] ?? 1) : ($speciesIds['Felino'] ?? 2);
                $petNames = $isDog 
                    ? ['Max', 'Luna', 'Rocky', 'Toby', 'Lola', 'Bruno', 'Pelusa', 'Coco', 'Kira', 'Thor']
                    : ['Michi', 'Salem', 'Cleo', 'Pelusa', 'Tom', 'Kitty', 'Simón', 'Félix', 'Minino', 'Mía'];
                
                $breedIds = $breedsBySpecies[$spId] ?? [];
                $razaId = !empty($breedIds) ? $breedIds[array_rand($breedIds)] : null;

                DB::table('pets')->insert([
                    'clinic_id'     => $this->clinicId,
                    'customer_id'   => $customerId,
                    'name'          => $petNames[array_rand($petNames)],
                    'species_id'    => $spId,
                    'raza_id'       => $razaId,
                    'gender'        => rand(0, 1) ? 'M' : 'H',
                    'color'         => ['Marrón', 'Negro', 'Blanco', 'Gris', 'Dorado', 'Atigrado'][array_rand(['Marrón', 'Negro', 'Blanco', 'Gris', 'Dorado', 'Atigrado'])],
                    'birth_date'    => Carbon::now()->subMonths(rand(3, 120))->format('Y-m-d'),
                    'current_weight'=> $isDog ? rand(3, 35) : rand(2, 6),
                    'esterilizado'  => rand(0, 1),
                    'fallecido'     => false,
                    'created_at'    => $createdAt,
                    'updated_at'    => $createdAt,
                ]);
            }
        }
        $this->command->info('  ✔ 200 Clientes y ~320 Mascotas creados.');
    }

    private function seedAppointmentsAndRecords(): void
    {
        $pets = DB::table('pets')->select('id', 'customer_id', 'species_id', 'current_weight')->get();
        $reasonIds = DB::table('appointment_reasons')->pluck('id', 'name')->toArray();

        // Estructura de Enfermedades con sus Síntomas correspondientes para simular estacionalidad y relaciones coherentes
        $clinicalData = [
            'Verano' => [ // Meses de calor (Diciembre a Marzo) -> Más Dermatitis y Otitis
                ['enfermedad' => 'Dermatitis alérgica por pulgas', 'sintoma' => 'Prurito excesivo y enrojecimiento en la piel'],
                ['enfermedad' => 'Dermatitis alérgica por pulgas', 'sintoma' => 'Pérdida de pelaje y picazón constante'],
                ['enfermedad' => 'Otitis externa bilateral', 'sintoma' => 'Sacudidas frecuentes de cabeza y mal olor en oídos'],
                ['enfermedad' => 'Gastroenteritis aguda', 'sintoma' => 'Vómitos frecuentes y decaimiento'],
                ['enfermedad' => 'Paciente sano - control preventivo', 'sintoma' => 'Chequeo preventivo general y desparasitación'],
            ],
            'Invierno' => [ // Meses de frío (Junio a Septiembre) -> Más Problemas Respiratorios
                ['enfermedad' => 'Tos de las perreras (traqueobronquitis)', 'sintoma' => 'Tos seca persistente y secreción nasal'],
                ['enfermedad' => 'Rinotraqueitis felina', 'sintoma' => 'Estornudos constantes y secreción ocular abundante'],
                ['enfermedad' => 'Gastroenteritis aguda', 'sintoma' => 'Diarrea líquida y falta de apetito'],
                ['enfermedad' => 'Otitis externa bilateral', 'sintoma' => 'Rascado constante de orejas'],
                ['enfermedad' => 'Paciente sano - control preventivo', 'sintoma' => 'Vacunación anual de refuerzo'],
            ],
            'Transición' => [ // Abril, Mayo, Octubre, Noviembre -> Equilibrado
                ['enfermedad' => 'Ehrlichiosis canina', 'sintoma' => 'Fiebre alta, inapetencia y encías pálidas'],
                ['enfermedad' => 'Otitis externa bilateral', 'sintoma' => 'Mal olor y dolor al tocar la oreja'],
                ['enfermedad' => 'Dermatitis alérgica por pulgas', 'sintoma' => 'Rasguños y lesiones en zona lumbar'],
                ['enfermedad' => 'Gastroenteritis aguda', 'sintoma' => 'Deposiciones blandas y vómitos'],
                ['enfermedad' => 'Paciente sano - control preventivo', 'sintoma' => 'Revisión anual y control de peso'],
            ]
        ];

        $appointments = [];
        $records = [];
        $appointmentIdCounter = 1;

        $totalMonths = (int)$this->startDate->diffInMonths($this->endDate) + 1;

        // Generar citas en todo el rango
        for ($month = 0; $month < $totalMonths; $month++) {
            $monthStart = $this->startDate->copy()->addMonths($month);
            $monthEnd = $monthStart->copy()->endOfMonth();
            if ($monthEnd->isAfter($this->endDate)) $monthEnd = $this->endDate->copy();

            // Determinar la estación del mes
            $monthNumber = $monthStart->month;
            if (in_array($monthNumber, [12, 1, 2, 3])) {
                $season = 'Verano';
            } elseif (in_array($monthNumber, [6, 7, 8, 9])) {
                $season = 'Invierno';
            } else {
                $season = 'Transición';
            }

            // Generar ~150 citas por mes
            for ($c = 0; $c < 150; $c++) {
                $pet = $pets->random();
                $apptDate = $monthStart->copy()->addDays(rand(0, 27))->setTime(rand(8, 17), rand(0, 3) * 15, 0);

                if ($apptDate->isFuture()) {
                    $status = rand(0, 1) ? 'CONFIRMADA' : 'PENDIENTE';
                } else {
                    // Histórico: 85% completadas, 10% canceladas, 5% excedido
                    $randVal = rand(1, 100);
                    $status = $randVal <= 85 ? 'COMPLETADA' : ($randVal <= 95 ? 'CANCELADA' : 'EXCEDIDO');
                }

                $clinicalCase = $clinicalData[$season][array_rand($clinicalData[$season])];
                $reasonName = str_contains($clinicalCase['enfermedad'], 'sano') ? 'Vacunación' : 'Consulta General';
                $reasonId = $reasonIds[$reasonName] ?? 1;
                $vetId = $this->vetIds[array_rand($this->vetIds)];

                // Insertar cita
                DB::table('appointments')->insert([
                    'id'                    => $appointmentIdCounter,
                    'clinic_id'             => $this->clinicId,
                    'customer_id'           => $pet->customer_id,
                    'pet_id'                => $pet->id,
                    'veterinarian_id'       => $vetId,
                    'fecha_hora'            => $apptDate,
                    'end_time'              => $apptDate->copy()->addMinutes(30)->format('H:i:s'),
                    'reason'                => $clinicalCase['sintoma'],
                    'appointment_reason_id' => $reasonId,
                    'status'                => $status,
                    'notes'                 => 'Cita programada vía sistema.',
                    'created_at'            => $apptDate->copy()->subDays(rand(1, 5)),
                    'updated_at'            => $apptDate,
                ]);

                // Si está completada, generar historia clínica y receta
                if ($status === 'COMPLETADA') {
                    $recordId = DB::table('medical_records')->insertGetId([
                        'clinic_id'                => $this->clinicId,
                        'pet_id'                   => $pet->id,
                        'veterinarian_id'          => $vetId,
                        'appointment_id'           => $appointmentIdCounter,
                        'date'                     => $apptDate,
                        'reason'                   => $clinicalCase['sintoma'],
                        'weight'                   => round($pet->current_weight + (rand(-10, 10) / 10), 2),
                        'temperature'              => round(rand(378, 395) / 10, 1),
                        'heart_rate'               => $pet->species_id == 2 ? rand(120, 200) : rand(70, 140),
                        'respiratory_rate'         => rand(15, 35),
                        'anamnesis'                => 'Paciente acude por consulta de triaje general. ' . $clinicalCase['sintoma'],
                        'diagnostico_presuntivo'   => $clinicalCase['enfermedad'],
                        'tratamiento_indicaciones' => 'Tratamiento sintomático de soporte según protocolo de la clínica.',
                        'created_at'               => $apptDate,
                        'updated_at'               => $apptDate,
                    ]);

                    // Sembrar receta correspondiente si no es paciente sano
                    if (!str_contains($clinicalCase['enfermedad'], 'sano')) {
                        $this->seedPrescriptionForRecord($recordId, $clinicalCase['enfermedad'], $apptDate);
                    }
                }

                $appointmentIdCounter++;
            }
        }
        $this->command->info('  ✔ Citas e Historias Clínicas (con Recetas Médicas vinculadas) creadas con lógica estacional.');
    }

    private function seedPrescriptionForRecord(int $recordId, string $disease, Carbon $date): void
    {
        $prescriptionData = match ($disease) {
            'Dermatitis alérgica por pulgas' => [
                'name' => 'Fipronil Pipeta Perro 10-20kg',
                'dosage' => '1 pipeta',
                'frequency' => 'Cada 30 días',
                'duration' => '1 aplicación',
                'via' => 'TÓPICA',
                'days' => 1,
                'qty' => 1,
                'indicaciones' => 'Aplicar directamente sobre la piel de la nuca.'
            ],
            'Otitis externa bilateral' => [
                'name' => 'Meloxicam 1.5mg/ml Gotas 15ml',
                'dosage' => '5 gotas',
                'frequency' => 'Cada 24 horas',
                'duration' => '5 días',
                'via' => 'ORAL',
                'days' => 5,
                'qty' => 1,
                'indicaciones' => 'Dar junto con el alimento.'
            ],
            'Gastroenteritis aguda' => [
                'name' => 'Cefalexina 250mg/5ml Suspensión',
                'dosage' => '2.5 ml',
                'frequency' => 'Cada 12 horas',
                'duration' => '7 días',
                'via' => 'ORAL',
                'days' => 7,
                'qty' => 1,
                'indicaciones' => 'Agitar bien antes de usar. Mantener refrigerado.'
            ],
            'Tos de las perreras (traqueobronquitis)', 'Ehrlichiosis canina' => [
                'name' => 'Doxiciclina 100mg x 100 cap',
                'dosage' => '1/2 tableta',
                'frequency' => 'Cada 12 horas',
                'duration' => '10 días',
                'via' => 'ORAL',
                'days' => 10,
                'qty' => 10,
                'indicaciones' => 'Administrar después de las comidas. No suspender tratamiento.'
            ],
            'Rinotraqueitis felina' => [
                'name' => 'Amoxicilina 500mg x 100 tab',
                'dosage' => '1/4 tableta',
                'frequency' => 'Cada 12 horas',
                'duration' => '7 días',
                'via' => 'ORAL',
                'days' => 7,
                'qty' => 4,
                'indicaciones' => 'Disolver en agua si es necesario para facilitar toma.'
            ],
            default => null
        };

        if ($prescriptionData) {
            $prodId = $this->medicationIds[$prescriptionData['name']] ?? null;
            DB::table('prescriptions')->insert([
                'clinic_id' => $this->clinicId,
                'medical_record_id' => $recordId,
                'product_id' => $prodId,
                'medicamento' => $prescriptionData['name'],
                'dosage' => $prescriptionData['dosage'],
                'frequency' => $prescriptionData['frequency'],
                    'duration' => $prescriptionData['duration'],
                'via_administracion' => $prescriptionData['via'],
                'duracion_dias' => $prescriptionData['days'],
                'indicaciones' => $prescriptionData['indicaciones'],
                'cantidad_dispensada' => $prescriptionData['qty'],
                'dispensado' => (bool)rand(0, 1),
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }

    private function seedInventoryAndSales(): void
    {
        $vetsAndAdmin = array_merge($this->vetIds, [$this->adminId]);
        $customers = DB::table('customers')->pluck('id')->toArray();
        $products = DB::table('products')->where('clinic_id', $this->clinicId)->get();

        $totalMonths = (int)$this->startDate->diffInMonths($this->endDate) + 1;

        // 1. Crear Cajas Registradoras mensuales para justificar ventas
        for ($month = 0; $month < $totalMonths; $month++) {
            $monthStart = $this->startDate->copy()->addMonths($month);
            $openedAt = $monthStart->copy()->setTime(8, 0, 0);
            $closedAt = $monthStart->copy()->endOfMonth()->setTime(20, 0, 0);

            DB::table('cash_registers')->insert([
                'user_id'           => $this->adminId,
                'opened_at'         => $openedAt,
                'closed_at'         => $closedAt,
                'opening_amount'    => 200.00,
                'calculated_amount' => 18500.00, // Aprox mensual
                'real_amount'       => 18500.00,
                'difference'        => 0.00,
                'status'            => 'CERRADA',
                'created_at'        => $openedAt,
                'updated_at'        => $closedAt,
            ]);
        }

        // 2. Generar 1800 ventas distribuidas
        $saleIdCounter = 1;
        $totalDays = (int)$this->startDate->diffInDays($this->endDate);

        for ($s = 0; $s < 1800; $s++) {
            $saleDate = $this->startDate->copy()->addDays(rand(0, $totalDays))->setTime(rand(9, 19), rand(0, 59), 0);
            $customerId = $customers[array_rand($customers)];
            $cajeroId = $vetsAndAdmin[array_rand($vetsAndAdmin)];

            // Seleccionar 1-3 productos aleatorios
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

                // Generar movimiento de inventario si no es servicio
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

            // Insertar Venta
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

            // Insertar Detalles
            DB::table('sale_details')->insert($details);

            $saleIdCounter++;
        }
        $this->command->info('  ✔ 1800 Ventas y movimientos de Kardex coherentes cargados.');
    }
}

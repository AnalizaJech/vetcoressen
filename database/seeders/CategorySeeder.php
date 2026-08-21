<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Categorías de inventario y servicios en inglés (estándar internacional).
     */
    public function run(): void
    {
        $categories = [
            'Antibiotics',
            'Analgesics and Anti-inflammatories',
            'Internal Antiparasitics',
            'External Antiparasitics',
            'Vaccines',
            'Vitamins and Supplements',
            'Dry Food (Kibble)',
            'Wet Food (Cans/Pouches)',
            'Medicated Diets',
            'Treats and Snacks',
            'Accessories (Leashes, Collars)',
            'Toys',
            'Hygiene (Shampoo, Soaps)',
            'Grooming',
            'Cat Litter',
            'Medical Instruments',
            'Surgical Supplies',
            'Clinical Services',
            'Dog/Cat Grooming',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category]);
        }
    }
}

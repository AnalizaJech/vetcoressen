<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Antibióticos',
            'Analgésicos y Antiinflamatorios',
            'Antiparasitarios Internos',
            'Antiparasitarios Externos',
            'Vacunas',
            'Vitaminas y Suplementos',
            'Alimento Seco (Croquetas)',
            'Alimento Húmedo (Latas/Sobres)',
            'Dietas Medicadas',
            'Snacks y Premios',
            'Accesorios (Correas, Collares)',
            'Juguetes',
            'Higiene (Shampoo, Jabones)',
            'Estética',
            'Arena para Gatos',
            'Instrumental Médico',
            'Material Quirúrgico',
            'Servicios Clínicos',
            'Peluquería Canina/Felina',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category]);
        }
    }
}

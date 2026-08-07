<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Species;
use App\Models\Breed;

class SpeciesBreedSeeder extends Seeder
{
    public function run(): void
    {
        $especiesData = [
            'Canino' => [
                'Mestizo', 'Affenpinscher', 'Akita', 'Akita Americano', 'Basset Hound', 'Beagle',
                'Bichón Frisé', 'Bichón Maltés', 'Border Collie', 'Bóxer', 'Bulldog Francés',
                'Bulldog Inglés', 'Bull Terrier', 'Caniche (Poodle)', 'Chihuahua', 'Chow Chow',
                'Cocker Spaniel', 'Corgi', 'Dálmata', 'Dóberman', 'Dogo Argentino',
                'Golden Retriever', 'Gran Danés', 'Husky Siberiano', 'Jack Russell Terrier',
                'Labrador Retriever', 'Mastín Napolitano', 'Pastor Alemán', 'Pastor Australiano',
                'Pastor Belga Malinois', 'Pekinés', 'Perro Sin Pelo del Perú', 'Pitbull',
                'Pomerania', 'Pug (Carlino)', 'Rottweiler', 'San Bernardo', 'Schnauzer',
                'Shar Pei', 'Shih Tzu', 'Staffordshire Bull Terrier', 'Teckel (Salchicha)',
                'Terrier de Boston', 'Yorkshire Terrier'
            ],
            'Felino' => [
                'Mestizo', 'Abisinio', 'Angora Turco', 'Azul Ruso', 'Bengalí', 'Bobtail Japonés',
                'Bombay', 'Bosque de Noruega', 'Británico de Pelo Corto', 'Burmés',
                'Esfinge (Sphynx)', 'Exótico de Pelo Corto', 'Himalayo', 'Maine Coon',
                'Mau Egipcio', 'Oriental', 'Persa', 'Ragdoll', 'Sagrado de Birmania',
                'Siamés', 'Siberiano', 'Singapura', 'Somalí', 'Toyger'
            ],
            'Ave' => [
                'Mestizo', 'Agapornis', 'Cacatúa', 'Canario', 'Cotorra', 'Diamante Mandarín',
                'Guacamayo', 'Loro Gris (Yaco)', 'Loro Amazónico', 'Ninfa (Carolina)',
                'Periquito Australiano', 'Tucán'
            ],
            'Roedor' => [
                'Mestizo', 'Chinchilla', 'Cobaya (Cuy)', 'Gerbo', 'Hámster Ruso', 'Hámster Sirio',
                'Hámster Roborovski', 'Ratón Doméstico', 'Rata Doméstica'
            ],
            'Lagomorfo' => [
                'Mestizo', 'Conejo Angora', 'Conejo Belier', 'Conejo Cabeza de León',
                'Conejo Enano (Netherland Dwarf)', 'Conejo Holandés', 'Conejo Rex', 'Conejo Toy'
            ],
            'Reptil' => [
                'Mestizo', 'Camaleón', 'Dragón Barbudo', 'Gecko Leopardo', 'Iguana',
                'Serpiente del Maíz', 'Pitón Bola', 'Tortuga de Agua', 'Tortuga de Tierra'
            ],
            'Otro' => [
                'Mestizo', 'Hurón', 'Erizo de Tierra', 'Cerdo Miniatura (Mini Pig)'
            ]
        ];

        foreach ($especiesData as $especieNombre => $razas) {
            $especie = Species::firstOrCreate(['name' => $especieNombre]);

            foreach ($razas as $razaNombre) {
                Breed::firstOrCreate([
                    'species_id' => $especie->id,
                    'name' => $razaNombre,
                ]);
            }
        }
    }
}

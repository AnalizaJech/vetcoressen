<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Species;
use App\Models\Breed;

class SpeciesBreedSeeder extends Seeder
{
    /**
     * Especies y razas en inglés para soporte internacional.
     */
    public function run(): void
    {
        $especiesData = [
            'Canine' => [
                'Mixed Breed', 'Affenpinscher', 'Akita', 'American Akita', 'Basset Hound', 'Beagle',
                'Bichon Frise', 'Maltese', 'Border Collie', 'Boxer', 'French Bulldog',
                'English Bulldog', 'Bull Terrier', 'Poodle (Standard/Toy)', 'Chihuahua', 'Chow Chow',
                'Cocker Spaniel', 'Corgi', 'Dalmatian', 'Doberman Pinscher', 'Dogo Argentino',
                'Golden Retriever', 'Great Dane', 'Siberian Husky', 'Jack Russell Terrier',
                'Labrador Retriever', 'Neapolitan Mastiff', 'German Shepherd', 'Australian Shepherd',
                'Belgian Malinois', 'Pekingese', 'Peruvian Hairless Dog', 'Pitbull Terrier',
                'Pomeranian', 'Pug', 'Rottweiler', 'Saint Bernard', 'Schnauzer',
                'Shar Pei', 'Shih Tzu', 'Staffordshire Bull Terrier', 'Dachshund',
                'Boston Terrier', 'Yorkshire Terrier'
            ],
            'Feline' => [
                'Mixed Breed', 'Abyssinian', 'Turkish Angora', 'Russian Blue', 'Bengal', 'Japanese Bobtail',
                'Bombay', 'Norwegian Forest Cat', 'British Shorthair', 'Burmese',
                'Sphynx', 'Exotic Shorthair', 'Himalayan', 'Maine Coon',
                'Egyptian Mau', 'Oriental Shorthair', 'Persian', 'Ragdoll', 'Birman',
                'Siamese', 'Siberian', 'Singapura', 'Somali', 'Toyger'
            ],
            'Bird' => [
                'Mixed Breed', 'Lovebird', 'Cockatoo', 'Canary', 'Parakeet', 'Zebra Finch',
                'Macaw', 'African Grey Parrot', 'Amazon Parrot', 'Cockatiel',
                'Budgerigar', 'Toucan'
            ],
            'Small Mammal' => [
                'Mixed Breed', 'Chinchilla', 'Guinea Pig', 'Gerbil', 'Russian Dwarf Hamster', 'Syrian Hamster',
                'Roborovski Hamster', 'Fancy Mouse', 'Fancy Rat'
            ],
            'Lagomorph' => [
                'Mixed Breed', 'Angora Rabbit', 'Lop Rabbit', 'Lionhead Rabbit',
                'Netherland Dwarf', 'Dutch Rabbit', 'Rex Rabbit', 'Toy Rabbit'
            ],
            'Reptile' => [
                'Mixed Breed', 'Chameleon', 'Bearded Dragon', 'Leopard Gecko', 'Green Iguana',
                'Corn Snake', 'Ball Python', 'Water Turtle', 'Tortoise'
            ],
            'Other' => [
                'Mixed Breed', 'Ferret', 'Hedgehog', 'Miniature Pig'
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

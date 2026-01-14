<?php

namespace Database\Seeders;

use App\Models\Coffee;
use Illuminate\Database\Seeder;

class CoffeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coffees = [
            [
                'name' => 'Espresso Intenso',
                'brand' => 'Illy',
                'description' => 'Tueste oscuro con notas de cacao y avellana.',
                'price' => 14500,
                'stock' => 120,
                'image_path' => 'coffees/espresso-intenso.jpg', // archivo en storage/app/public
                'image_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'House Blend',
                'brand' => 'Starbucks',
                'description' => 'Mezcla equilibrada de granos latinoamericanos.',
                'price' => 12500,
                'stock' => 80,
                'image_path' => 'coffees/house-blend.jpg',
                'image_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Colombia Supremo',
                'brand' => 'Juan Valdez',
                'description' => 'Cafe colombiano con acidez media y notas citricas.',
                'price' => 13800,
                'stock' => 100,
                'image_path' => null,
                'image_url' => 'https://via.placeholder.com/300x300.png?text=Colombia+Supremo',
                'is_active' => true,
            ],
            [
                'name' => 'Ethiopia Yirgacheffe',
                'brand' => 'Blue Bottle',
                'description' => 'Notas florales y frutales con cuerpo ligero.',
                'price' => 18900,
                'stock' => 60,
                'image_path' => null,
                'image_url' => 'https://via.placeholder.com/300x300.png?text=Ethiopia+Yirgacheffe',
                'is_active' => true,
            ],
            [
                'name' => 'Brasil Santos',
                'brand' => 'Lavazza',
                'description' => 'Cuerpo medio, dulce y con notas de chocolate.',
                'price' => 11000,
                'stock' => 150,
                'image_path' => 'coffees/brasil-santos.jpg',
                'image_url' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Decaf Moka',
                'brand' => 'Illy',
                'description' => 'Descafeinado suave para preparar moka o espresso.',
                'price' => 11800,
                'stock' => 70,
                'image_path' => null,
                'image_url' => 'https://via.placeholder.com/300x300.png?text=Decaf+Moka',
                'is_active' => true,
            ],
        ];

        foreach ($coffees as $coffee) {
            Coffee::updateOrCreate(
                ['name' => $coffee['name'], 'brand' => $coffee['brand']],
                $coffee
            );
        }
    }
}

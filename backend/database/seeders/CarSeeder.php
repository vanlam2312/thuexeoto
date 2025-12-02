<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        $cars = [
            [
                'name' => 'Toyota Vios 2020',
                'brand' => 'Toyota',
                'price' => 500000,
                'seats' => 5,
                'image_url' => 'https://example.com/vios.jpg',
                'description' => 'Xe tiết kiệm xăng, phù hợp gia đình.'
            ],
            [
                'name' => 'Mazda 3 2022',
                'brand' => 'Mazda',
                'price' => 800000,
                'seats' => 5,
                'image_url' => 'https://example.com/mazda3.jpg',
                'description' => 'Thiết kế sang trọng, lái êm.'
            ],
            [
                'name' => 'Kia Cerato 2021',
                'brand' => 'Kia',
                'price' => 700000,
                'seats' => 5,
                'image_url' => 'https://example.com/cerato.jpg',
                'description' => 'Không gian rộng rãi, xe mạnh mẽ.'
            ],
        ];

        foreach ($cars as $car) {
            Car::create($car);
        }
    }
}

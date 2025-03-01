<?php

namespace Database\Seeders;

use App\Models\Resturant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResturantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resturants = [
            [
                'name' => 'Resturant 1',
                'longitude' => '7.427628',
                'latitude' => '9.074912',
            ],
            [
                'name' => 'Resturant 2',
                'longitude' => '7.227628',
                'latitude' => '9.074112',
            ],
        ];

        Resturant::insert($resturants);
    }
}

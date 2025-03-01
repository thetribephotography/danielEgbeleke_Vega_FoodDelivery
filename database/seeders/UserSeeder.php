<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'DanTheAdmin',
                'email' => 'dantheadmin@vega.com',
                'role_id' => 1,
                'longitude' => '7.435089',
                'latitude' => '9.071348',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'DanTheUser',
                'email' => 'dantheuser@vega.com',
                'role_id' => 2,
                'longitude' => '7.483750',
                'latitude' => '9.068530',
                'password' => bcrypt('password'),
            ],
        ];

        User::insert($users);
    }
}

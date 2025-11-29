<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 30; $i++) {
            $name = $faker->name;
            DB::table('customers')->insert([
                'name' => $name, // Random Name
                'email' => $faker->unique()->safeEmail,
                'mobile_no' => '9' . $faker->unique()->numerify('#########'), //10-digit mobile number unique
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DenominationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $denominations = [
            ['value' => 2000, 'available_count' => 50],
            ['value' => 500,  'available_count' => 100],
            ['value' => 200,  'available_count' => 23],
            ['value' => 100,  'available_count' => 123],
            ['value' => 50,   'available_count' => 10],
            ['value' => 20,   'available_count' => 200],
            ['value' => 10,   'available_count' => 300],
        ];

        DB::table('denominations')->insert($denominations);
    }
}

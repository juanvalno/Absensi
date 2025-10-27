<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BagianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all departemen IDs
        $departemens = DB::table('departemens')->pluck('id')->toArray();

        // Define bagians with their respective departemen mappings
        $bagiansByDepartemen = [
            // HR bagians
            ['name' => 'Recruitment', 'departemen_index' => 0], // HR
            ['name' => 'Payroll', 'departemen_index' => 0], // HR
            ['name' => 'Training', 'departemen_index' => 0], // HR

            // Finance bagians
            ['name' => 'Accounting', 'departemen_index' => 1], // Finance
            ['name' => 'Budgeting', 'departemen_index' => 1], // Finance

            // IT bagians
            ['name' => 'Software Development', 'departemen_index' => 2], // IT
            ['name' => 'Network Administration', 'departemen_index' => 2], // IT

            // Marketing bagians
            ['name' => 'Digital Marketing', 'departemen_index' => 3], // Marketing
            ['name' => 'Brand Management', 'departemen_index' => 3], // Marketing

            // Operations bagians
            ['name' => 'Production', 'departemen_index' => 4], // Operations
            ['name' => 'Quality Control', 'departemen_index' => 4], // Operations

            // Sales bagians
            ['name' => 'Business Development', 'departemen_index' => 5], // Sales

            // Customer Service bagians
            ['name' => 'Customer Support', 'departemen_index' => 6], // Customer Service

            // R&D bagians
            ['name' => 'Product Research', 'departemen_index' => 7], // R&D

            // Legal bagians
            ['name' => 'Legal Affairs', 'departemen_index' => 8], // Legal

            // Administration bagians
            ['name' => 'Office Management', 'departemen_index' => 9], // Administration
        ];

        foreach ($bagiansByDepartemen as $bagian) {
            // Make sure we have a valid departemen index
            $departemenId = isset($departemens[$bagian['departemen_index']])
                ? $departemens[$bagian['departemen_index']]
                : null;

            DB::table('bagians')->insert([
                'id' => Str::uuid(),
                'id_departemen' => $departemenId,
                'name_bagian' => $bagian['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

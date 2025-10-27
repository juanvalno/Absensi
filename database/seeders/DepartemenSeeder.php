<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departemens = [
            'Human Resources',
            'Finance',
            'Information Technology',
            'Marketing',
            'Operations',
            'Sales',
            'Customer Service',
            'Research and Development',
            'Legal',
            'Administration'
        ];

        foreach ($departemens as $departemen) {
            DB::table('departemens')->insert([
                'id' => Str::uuid(),
                'name_departemen' => $departemen,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
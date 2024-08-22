<?php

namespace Database\Seeders;

use App\Models\Conta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Conta::factory()->state([
            'numero_conta' => 234,
            'saldo' => 180.37,
        ])->create();

        Conta::factory()->count(10)->create();
    }
}

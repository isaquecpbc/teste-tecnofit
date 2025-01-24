<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Movement;

class MovementSeeder extends Seeder
{
    public function run()
    {
        $movements = [
            'Corrida Maluca',
            'Boonta Eve',
            'GP Autódromo de Interlagos',
        ];

        foreach ($movements as $name) {
            Movement::create(['name' => $name]);
        }
    }
}

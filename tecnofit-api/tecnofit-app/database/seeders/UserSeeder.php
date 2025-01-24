<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            'Isaque Desenvolvedor',
            'Um Testador',
            'Anakin Skywalker',
            'Sebulba',
            'Ben Quadinaros',
            'Teemto Pagalies',
            'José Carlos Pace',
            'Emerson Fittipaldi',
            'Felipe Massa',
            'Rubens Barrichello',
            'Gabriel Bortoleto',
            'Ayrton Senna',
        ];

        foreach ($users as $name) {
            User::create(['name' => $name]);
        }
    }
}
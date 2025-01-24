<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersonalRecord;
use App\Models\User;
use App\Models\Movement;
use Carbon\Carbon;

class PersonalRecordSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $movements = Movement::all();

        foreach ($users as $user) {
            foreach ($movements as $movement) {
                // Gerar um value aleatório entre 70 e 90 em float
                $value = mt_rand(70 * 10, 90 * 10) / 10;
                
                // Gerar uma data aleatória do ano passado (2024)
                $dt_record = Carbon::create(2024, mt_rand(1, 12), mt_rand(1, 28), mt_rand(6, 23), mt_rand(0, 59), mt_rand(0, 59))->format('Y-m-d H:i:s');

                PersonalRecord::create([
                    'user_id' => $user->id,
                    'movement_id' => $movement->id,
                    'value' => $value,
                    'dt_record' => $dt_record,
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create([
            'name' => 'Luis Claudio',
            'email' => 'lclaudio@dirislimaeste.gob.pe',
            'password' => Hash::make('Lr#hCfmKBE75*#W'),
            'document_number' => "46794282",
            'CodigoNivelEESSId' => 2,
            'CodigoProfesionalId' =>"01c79387-fb41-4bac-a160-569e15d72b08"
        ]);

        User::create([
            'name' => 'Dijeim Solun Manrique Rodriguez',
            'email' => 'dmanrique@hospitalchosica.gob.pe',
            'password' => Hash::make('j7l8F2XqKZQSEyt'),
            'document_number' => "72000870",
            'CodigoNivelEESSId' => 2,
            'CodigoProfesionalId' =>"dd4a3079-78a0-4cba-bea7-79b599aa9e60"
        ]);
    }
}

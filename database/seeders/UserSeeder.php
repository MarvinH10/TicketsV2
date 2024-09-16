<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Sede;
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
        User::create([
            'name' => 'Marvin',
            'apellidos' => 'Campos Deza',
            'email' => 'marvinhectorcamposdeza@udh.edu.pe',
            'password' => Hash::make('987654321'),
            'celular' => '987654321',
            'activo' => true,
            'rol_id' => 1,
            'sed_id' => 1,
        ]);

        User::create([
            'name' => 'Jean Richard',
            'apellidos' => 'Lino Becerra',
            'email' => 'jeanlb.14.d@udh.edu.pe',
            'password' => Hash::make('914210336'),
            'celular' => '914210336',
            'activo' => true,
            'rol_id' => 1,
            'sed_id' => 1,
        ]);

        User::create([
            'name' => 'Cristina',
            'apellidos' => 'Cardenas Abarrote',
            'email' => 'cristina@udh.edu.pe',
            'password' => Hash::make('password123'),
            'celular' => '987654322',
            'activo' => true,
            'rol_id' => 2,
            'sed_id' => 2,
        ]);

        User::create([
            'name' => 'Rosario',
            'apellidos' => 'Beteta Arteaga',
            'email' => 'rosario@udh.edu.pe',
            'password' => Hash::make('password123'),
            'celular' => '987654323',
            'activo' => true,
            'rol_id' => 3,
            'sed_id' => 3,
        ]);
    }
}

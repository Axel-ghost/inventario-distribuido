<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Axel',
            'apellidos' => 'Admin',
            'usuario' => 'admin',
            'email' => 'admin@corebuild.com',
            'password' => '12345678',
            'rol' => 'administrador',
            'estado' => true,
        ]);
    }
}

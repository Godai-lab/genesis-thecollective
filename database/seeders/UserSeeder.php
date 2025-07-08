<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** Creando Usuarios **/
        $usersuperadmin = User::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
            'status' => true
        ]);
        $useradmin = User::create([
            'name' => 'Agente 1',
            'username' => 'agente1',
            'email' => 'test1@admin.com',
            'password' => Hash::make('agente1'),
            'status' => true
        ]);

        /** Creando Roles **/
        $rolsuperadmin = Role::create([
            'name' => 'Super Admin',
            'slug' => 'superadmin',
            'description' => 'Super Administrator',
            'full_access' => 1
        ]);
        $roladmin = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Usuario Administrador',
            'full_access' => 0
        ]);
        $rolh = Role::create([
            'name' => 'Todas las herramientas',
            'slug' => 'all',
            'description' => 'Todas las herramientas',
            'full_access' => 0
        ]);
        $rolh = Role::create([
            'name' => 'Herramienta 1',
            'slug' => 'herramienta1',
            'description' => 'Herramienta 1',
            'full_access' => 0
        ]);
        $rolh = Role::create([
            'name' => 'Herramienta 2',
            'slug' => 'herramienta2',
            'description' => 'Herramienta 2',
            'full_access' => 0
        ]);
        $rolh = Role::create([
            'name' => 'Herramienta 3',
            'slug' => 'herramienta3',
            'description' => 'Herramienta 3',
            'full_access' => 0
        ]);

        /** asignando usuarios a roles **/
        $usersuperadmin->roles()->sync([$rolsuperadmin->id]);
        $useradmin->roles()->sync([$roladmin->id]);
    }
}

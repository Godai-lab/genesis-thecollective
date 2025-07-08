<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        
        //permission Usuario
        $permission = Permission::create([
            'name' => 'Listar usuarios',
            'slug' => 'user.index',
            'description' => 'El usuario puede listar los usuarios',
        ]);

        $permission = Permission::create([
            'name' => 'Crear usuarios',
            'slug' => 'user.create',
            'description' => 'El usuario puede crear nuevos usuarios',
        ]);

        $permission = Permission::create([
            'name' => 'Editar usuarios',
            'slug' => 'user.edit',
            'description' => 'El usuario puede editar datos de los usuarios',
        ]);

        $permission = Permission::create([
            'name' => 'Eliminar usuarios',
            'slug' => 'user.destroy',
            'description' => 'El usuario puede eliminar usuarios',
        ]);

        //permission role
        $permission = Permission::create([
            'name' => 'Listar roles',
            'slug' => 'role.index',
            'description' => 'El usuario puede listar los roles',
        ]);

        $permission = Permission::create([
            'name' => 'Crear roles',
            'slug' => 'role.create',
            'description' => 'El usuario puede crear nuevos roles',
        ]);

        $permission = Permission::create([
            'name' => 'Editar roles',
            'slug' => 'role.edit',
            'description' => 'El usuario puede editar datos de los roles',
        ]);

        $permission = Permission::create([
            'name' => 'Eliminar roles',
            'slug' => 'role.destroy',
            'description' => 'El usuario puede eliminar roles',
        ]);

        // permission accounts
        $permission = Permission::create([
            'name' => 'Listar Cuentas',
            'slug' => 'account.index',
            'description' => 'El usuario puede listar las Cuentas',
        ]);

        $permission = Permission::create([
            'name' => 'Crear Cuentas',
            'slug' => 'account.create',
            'description' => 'El usuario puede crear nuevas Cuentas',
        ]);

        $permission = Permission::create([
            'name' => 'Editar Cuentas',
            'slug' => 'account.edit',
            'description' => 'El usuario puede editar datos de las Cuentas',
        ]);

        $permission = Permission::create([
            'name' => 'Eliminar Cuentas',
            'slug' => 'account.destroy',
            'description' => 'El usuario puede eliminar Cuentas',
        ]);
        // permission Generados
        $permission = Permission::create([
            'name' => 'Listar Generados',
            'slug' => 'generated.index',
            'description' => 'El usuario puede listar las Generados',
        ]);

        $permission = Permission::create([
            'name' => 'Crear Generados',
            'slug' => 'generated.create',
            'description' => 'El usuario puede crear nuevos Generados',
        ]);

        $permission = Permission::create([
            'name' => 'Editar Generados',
            'slug' => 'generated.edit',
            'description' => 'El usuario puede editar datos de los Generados',
        ]);

        $permission = Permission::create([
            'name' => 'Eliminar Generados',
            'slug' => 'generated.destroy',
            'description' => 'El usuario puede eliminar Generados',
        ]);

        // permission herramientas
        $permission = Permission::create([
            'name' => 'Permitir Generar Brief',
            'slug' => 'brief.index',
            'description' => 'El usuario puede usar brief',
        ]);

        $permission = Permission::create([
            'name' => 'Permitir Generar Génesis',
            'slug' => 'genesis.index',
            'description' => 'El usuario puede usar genesis',
        ]);

        $permission = Permission::create([
            'name' => 'Permitir Asistente Creativo',
            'slug' => 'asistentecreativo.index',
            'description' => 'El usuario puede usar asistente creativo',
        ]);

        $permission = Permission::create([
            'name' => 'Permitir Asistente Social media',
            'slug' => 'asistentesocialmedia.index',
            'description' => 'El usuario puede usar asistente social media',
        ]);

        $permission = Permission::create([
            'name' => 'Permitir Asistente Gráfica',
            'slug' => 'asistentegrafica.index',
            'description' => 'El usuario puede usar asistente gráfica',
        ]);
    }
}

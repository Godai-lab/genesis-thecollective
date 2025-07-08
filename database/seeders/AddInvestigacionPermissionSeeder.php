<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
class AddInvestigacionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission = Permission::create([
            'name' => 'Permitir Generar Investigación',
            'slug' => 'investigacion.index',
            'description' => 'El usuario puede usar la herramienta de investigación',
        ]);
    }
}

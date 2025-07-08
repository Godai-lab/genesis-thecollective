<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsEdicionImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
             [
                'name' => 'Permitir Edición de Imagen',
                'slug' => 'edit.image',
                'description' => 'El usuario puede editar imágenes en la herramienta de generación',
            ],
            [
                'name' => 'Permitir Expansión de Imagen',
                'slug' => 'edit.expand.image',
                'description' => 'El usuario puede expandir imágenes en la herramienta de generación',
            ],
            [
                'name' => 'Permitir Rellenar Imagen',
                'slug' => 'edit.fill.image',
                'description' => 'El usuario puede rellenar áreas faltantes en una imagen en la herramienta de generación',
            ],
        ];

        foreach ($permissions as $data) {
            Permission::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}

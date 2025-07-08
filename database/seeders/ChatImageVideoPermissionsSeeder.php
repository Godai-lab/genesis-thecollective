<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatImageVideoPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $permissions = [
            [
                'name' => 'Permitir Usar Chat',
                'slug' => 'chat.index',
                'description' => 'El usuario puede usar el chat del sistema',
            ],
            [
                'name' => 'Permitir Generar Imagen',
                'slug' => 'generador.imagen',
                'description' => 'El usuario puede usar el generador de imágenes',
            ],
            [
                'name' => 'Permitir Generar Video',
                'slug' => 'generador.video',
                'description' => 'El usuario puede usar el generador de videos',
            ],
        ];

        foreach ($permissions as $data) {
            Permission::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}

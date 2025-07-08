<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AccountSeeder::class,                            
            UserSeeder::class,                              
            PermissionSeeder::class,                         
            AddInvestigacionPermissionSeeder::class,
            ChatImageVideoPermissionsSeeder::class,          
            PermissionsEdicionImageSeeder::class,            
        ]);
    }
}

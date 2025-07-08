<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        /** Creando Cuenta **/
        $account = Account::create([
            'name' => 'cuenta 1',
            'description' => 'Cuenta 1',
            'status' => true
        ]);

        $account = Account::create([
            'name' => 'cuenta 2',
            'description' => 'Cuenta 2',
            'status' => true
        ]);
    }
}

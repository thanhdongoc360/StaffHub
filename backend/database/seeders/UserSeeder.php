<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'accountant_1',
            'email' => 'accountant1@staffhub.com',
            'password' => Hash::make('123456'),
            'role' => 'accountant',
            'role_id' => 4
        ]);
    }
}

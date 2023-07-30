<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            'name' => 'takaki',
            'email' => 'takaki55730317@gmail.com',
            'password' => Hash::make('5t5a7k3a'),
            'created_at' => now(),
        ]);
    }
}

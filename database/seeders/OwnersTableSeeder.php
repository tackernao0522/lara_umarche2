<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OwnersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('owners')->insert([
            [
                'name' => 'naomi',
                'email' => 'takaproject777@gmail.com',
                'password' => Hash::make('czk68346'),
                'created_at' => now(),
            ],
            [
                'name' => 'kaira',
                'email' => 'takaki5573031@yahoo.co.jp',
                'password' => Hash::make('ggz6kxp3'),
                'created_at' => now(),
            ],
            [
                'name' => 'rikito',
                'email' => 'takaki5573@hotmail.com',
                'password' => Hash::make('czk5573'),
                'created_at' => now(),
            ],
        ]);
    }
}

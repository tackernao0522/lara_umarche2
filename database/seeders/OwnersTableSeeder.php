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
                'name' => 'kaira',
                'email' => 'takaproject777@gmail.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
            ],
            [
                'name' => 'pepe',
                'email' => 'cheap_trick_magic@yahoo.co.jp',
                'password' => Hash::make('password123'),
                'created_at' => now(),
            ],
            [
                'name' => 'mieko',
                'email' => 'takaki_5573031@yahoo.co.jp',
                'password' => Hash::make('password123'),
                'created_at' => now(),
            ],
            [
                'name' => 'test1',
                'email' => 'test1@test.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
            ],
            [
                'name' => 'test2',
                'email' => 'test2@test.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
            ],
            [
                'name' => 'test3',
                'email' => 'test3@test.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
            ],
        ]);
    }
}

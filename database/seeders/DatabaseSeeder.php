<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminsTableSeeder::class,
            OwnersTableSeeder::class,
            ShopsTableSeeder::class,
            ImagesTableSeeder::class,
            CategoriesTableSeeder::class,
            // ProductsTableSeeder::class,
            // StocksTableSeeder::class,
        ]);

        // \App\Models\User::factory(10)->create();
    }
}

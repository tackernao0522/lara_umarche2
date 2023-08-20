<?php

namespace Database\Seeders;

use App\Models\Stock;
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
            UsersTableSeeder::class,
        ]);

        // \App\Models\User::factory(10)->create();
        Stock::factory(100)->create();
    }
}

<?php

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            ApiFeatureSeeder::class
        ]);
    }
}

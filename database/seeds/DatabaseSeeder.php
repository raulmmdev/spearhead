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
            SiteSeeder::class,
            SiteProviderFeatureSeeder::class,
            ApiFeatureSeeder::class,

            //SiteCategorySeeder::class,
            //ProductSeeder::class,
        ]);
    }
}

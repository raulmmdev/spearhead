<?php

use Illuminate\Database\Seeder;
use App\Model\Entity\Site;

/**
 * SiteSeeder
 */
class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $site = new Site();
        $site->name = 'Test Site';
        $site->save();
    }
}

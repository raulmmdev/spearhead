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
        $user = new Site();
        $user->name = 'a test shop';
        $user->save();
    }
}

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
        $user = App\Model\Entity\User::find(App\Model\Entity\User::pluck('id')[1]);

        $site = new Site();
        $site->name = 'A test shop';
        $site->user()->associate($user);

        $site->save();
    }
}

<?php

use Illuminate\Database\Seeder;
use App\Model\Entity\SiteProviderFeature;

class SiteProviderFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = App\Model\Entity\User::find(App\Model\Entity\User::pluck('id')[1]);

        $feature = new SiteProviderFeature();
        $feature->user()->associate($user);
        $feature->login = 'login';
        $feature->key = 'key';
        $feature->status = SiteProviderFeature::STATUS_ENABLED;

        $feature->save();
    }
}

<?php

use App\Model\Entity\ApiFeature;
use App\Model\Entity\User;
use Illuminate\Database\Seeder;

class ApiFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::find(App\Model\Entity\User::pluck('id')[0]);
        $site = User::find(App\Model\Entity\Site::pluck('id')[0]);

        $feature = new ApiFeature();
        $feature->user()->associate($user);
        $feature->site()->associate($site);
        $feature->login = 'login';
        $feature->key = 'key';
        $feature->status = ApiFeature::STATUS_ENABLED;
        $feature->save();
    }
}

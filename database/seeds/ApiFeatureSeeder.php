<?php

use Illuminate\Database\Seeder;
use App\Model\Entity\ApiFeature;

class ApiFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = App\Model\Entity\User::find(App\Model\Entity\User::pluck('id')[0]);

        $feature = new ApiFeature();
        $feature->user()->associate($user);
        $feature->login = 'login';
        $feature->key = 'key';
        $feature->status = ApiFeature::STATUS_ENABLED;

        $feature->save();
    }
}

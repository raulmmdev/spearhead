<?php

use Illuminate\Database\Seeder;
use App\Model\Entity\User;

/**
 * UserSeeder
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'Geralt de Rivia';
        $user->email = 'geralt.rivia@multisafepay.com';
        $user->country = 'NL';
        $user->password = bcrypt('plokiju123');
        $user->save();

        $user = new User();
        $user->name = 'Multisafepay';
        $user->email = 'msp@multisafepay.com';
        $user->password = bcrypt('plokiju123');
        $user->country = 'NL';
        $user->save();
    }
}

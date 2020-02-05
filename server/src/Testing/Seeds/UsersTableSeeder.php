<?php

use Illuminate\Database\Seeder;
use Lab19\Cart\Model\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(User::class, 10)->create();
    }
}

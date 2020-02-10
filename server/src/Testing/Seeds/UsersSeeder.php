<?php

namespace Gernzy\Server\Testing\Seeds;

use Illuminate\Database\Seeder;
use Gernzy\Server\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin = factory(User::class)->create();
        $admin->email = 'admin@example.com';
        $admin->is_admin = 1;
        $admin->save();

        $user = factory(User::class)->create();
        $user->email = 'user@example.com';
        $user->save();
        // factory(User::class, 50)->create()->each(function ($user) {


        // });
    }
}

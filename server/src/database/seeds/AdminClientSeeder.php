<?php

namespace Gernzy\Server\Database\Seeds;

use Gernzy\Server\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// use Illuminate\Support\Facades\DB;

class AdminClientSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $userName = Str::random(6);
        $email = $userName . '@gernzy.com';
        $password = Str::random(12);

        $user = User::create([
            'name' => $userName,
            'email' => $email,
            'is_admin' => true,
            'password' => Hash::make($password)
        ]);

        print " ******************** 
        \n\e[32m Admin client successfully created.  \e[39m
        \n Username: " . $user->name . " 
        \n Email: " . $user->email . "
        \n Password: " . $password . "  
        \n ******************** \n";
    }
}

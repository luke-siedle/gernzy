<?php

namespace Lab19\Cart\Services;

use Illuminate\Http\Request;
use Lab19\Cart\Models\User;

class UserService
{

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getFromToken(String $token)
    {
        return User::where('session_token', '=', $token)->first();
    }

}

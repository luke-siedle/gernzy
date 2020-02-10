<?php

namespace Gernzy\Server\Services;

use Illuminate\Http\Request;
use Gernzy\Server\Models\User;

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

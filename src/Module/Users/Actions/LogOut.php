<?php

namespace Lab19\Cart\Module\Users\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lab19\Cart\Module\Users\User;
use Lab19\Cart\Module\Users\Services\SessionService;

class LogOut
{
    public function __construct( SessionService $session, Request $request ){
        $this->session = $session;
        $this->request = $request;
    }

    public function handle(){

        $token = $this->request->bearerToken();

        if( $token ){
            $user = User::where('session_token', '=', $token)->first();
            if( $user ){
                // Disaccociate the session
                // and delete it
                $user->session_token = null;
                $user->save();
                return $this->session->close();
            }

            // Where it's a user-less session
            // and the session exists
            if( $this->session->exists() && $token === $this->session->getToken() ){
                return $this->session->close();
            }
        }

        return false;
    }
}

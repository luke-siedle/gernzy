<?php

namespace Lab19\Cart\Module\Users\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lab19\Cart\Module\Users\User;
use Lab19\Cart\Module\Users\Services\SessionService;

class LogIn
{
    public function __construct( SessionService $session, Request $request ){
        $this->session = $session;
        $this->request = $request;
    }

    public function handle( String $email, String $password ){

        // If the user already had a session,
        // allow this to merge if possible
        if( $token = $this->request->bearerToken() ){
            $this->session->setFromToken( $token );
        } else {
            // Persist a new session
            $this->session->save();
        }

        if( Auth::guard('cart')->attempt([
                'email' => $email,
                'password' => $password
            ])){
            $user = Auth::guard('cart')->user();

            // Update the user's current token
            $user->session_token = $this->session->getToken();
            $user->save();

            return [
                'user' => $user,
                'token' => $this->session->getToken()
            ];

        } else {
            return false;
        }
    }
}

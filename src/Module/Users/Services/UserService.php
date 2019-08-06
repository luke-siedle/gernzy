<?php

    namespace Lab19\Cart\Module\Users\Services;

    use Illuminate\Support\Str;
    use Lab19\Cart\Module\Users\User;
    use Illuminate\Support\Facades\Auth;
    use App;

    class UserService {

        public function logIn( String $email, String $password, String $token = null ){

            $session = App::make('Lab19\SessionService');

            if( $token ){
                // Allow merge of session
                $session->setFromToken( $token );
            } else {
                // Persist a new session
                $session->save();
            }

            if(Auth::guard('cart')->attempt([
                    'email' => $email,
                    'password' => $password
                ])){
                $user = Auth::guard('cart')->user();

                // Update the user's current token
                $user->session_token = $session->getToken();
                $user->save();

                return [
                    'user' => $user,
                    'token' => $session->getToken()
                ];
            } else {
                return false;
            }
        }

        public function getFromToken( String $token ){
            return User::where('session_token', '=', $token)->first();
        }

    }

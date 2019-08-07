<?php

    namespace Lab19\Cart\Module\Users\Services;

    use Illuminate\Support\Str;
    use Lab19\Cart\Module\Users\User;
    use Lab19\Cart\Module\Users\Session;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Http\Request;
    use App;

    class UserService {

        public function __construct( Request $request ){
            $this->request = $request;
        }

        public function getUser(){
            $this->request->session->load('user');
            return $this->request->session->user;
        }

        public function logIn( String $email, String $password ){

            $session = App::make('Lab19\SessionService');

            // If the user already had a session,
            // allow this to merge
            if( $token = $this->request->bearerToken() ){
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

        public function logOut(){
            $session = App::make('Lab19\SessionService');
            $token = $this->request->bearerToken();
            if( $token ){
                $user = User::where('session_token', '=', $token)->first();
                if( $user ){
                    // Disaccociate the session
                    $user->session_token = null;
                    $user->save();
                    return $session->close();
                }

                // Where it's a user-less session
                // and the session exists
                if( $session->exists() && $token === $session->getToken() ){
                    return $session->close();
                }

                return false;
            }

            return false;
        }

        public function getFromToken( String $token ){
            return User::where('session_token', '=', $token)->first();
        }

    }

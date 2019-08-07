<?php

    namespace Lab19\Cart\Module\Users\Services;

    use Illuminate\Support\Str;
    use Lab19\Cart\Module\Users\Session;
    use Lab19\Cart\Module\Orders\Cart;
    use Illuminate\Http\Request;

    class SessionService {

        const NAMESPACE = 'cart';

        public function __construct( Request $request ){
            $this->session = new Session;
            $this->session->token = Str::random(60);
            $this->session->data = [
                'cart_uuid' => Str::uuid()
            ];
            if( $request->bearerToken() ){
                $this->session->token = $request->bearerToken();
            }
        }

        public function setFromToken( $token ){
            $session = Session::where('token', '=', $token )->first();
            if( !$session ){
                // TODO: This is dangerous, we don't want to give users the ability
                // to arbitrarily set their token due to CSRF.
                // We need to review where and why this is used.
                // A token should only be creatable from the createSession method
                // in GraphQL
                $this->session->token = $token;
                $this->session->save();
            } else {
                $this->session = $session;
                return $this->session;
            }
        }

        public function getFromToken( $token ){
            $session = Session::where('token', '=', $token )->first();
            return $session;
        }

        public function setUserId( Int $id ){
            $this->session->user_id = $id;
            $this->session->save();
        }

        public function update( Array $array ){
            $this->session->data = array_merge( $this->session->data, $array );
            $this->save();
        }

        public function save(){
            $this->session->save();
        }

        public function close(){
            return $this->session->delete();
        }

        public function get( $key = null ){
            if( $key ){
                return $this->session->data[ $key ] ?? null;
            }
            return $this->session->data;
        }

        public function getToken(){
            return $this->session->token;
        }

    }

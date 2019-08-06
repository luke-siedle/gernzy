<?php

    namespace Lab19\Cart\Module\Users\Services;

    use Illuminate\Support\Str;
    use Lab19\Cart\Module\Users\Session;

    class SessionService {

        const NAMESPACE = 'cart';

        public function __construct(){
            $this->session = new Session;
            $this->session->token = Str::random(60);
            $this->session->data = [
                'cart_uuid' => Str::uuid()
            ];
        }

        public function setFromToken( $token ){
            $session = Session::where('token', '=', $token )->first();
            if( !$session ){
                $this->session->token = $token;
                $this->session->save();
            } else {
                $this->session = $session;
            }
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

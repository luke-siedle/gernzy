<?php

    namespace Lab19\Cart\Module\Users\Repository;

    use Lab19\Cart\Module\Users;

    class SessionRepository {
        public static function create( $attributes ){
            $session = new Session($attributes);
            $session->save();
            return $session;
        }
    }

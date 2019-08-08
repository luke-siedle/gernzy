<?php

    namespace Lab19\Cart\Module\Users\Services;

    use Illuminate\Support\Str;
    use Lab19\Cart\Module\Users\User;
    use Lab19\Cart\Module\Users\Session;
    use Lab19\Cart\Module\Users\Services\SessionService;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Http\Request;
    use App;

    class UserService {

        public function __construct( Request $request, SessionService $sessionService ){
            $this->request = $request;
        }

        public function getFromToken( String $token ){
            return User::where('session_token', '=', $token)->first();
        }

    }

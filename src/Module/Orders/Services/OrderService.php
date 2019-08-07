<?php

    namespace Lab19\Cart\Module\Orders\Services;

    use Illuminate\Support\Str;
    use Lab19\Cart\Module\Users\User;
    use Lab19\Cart\Module\Orders\Cart;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Http\Request;
    use App;

    class OrderService {

        public function __construct( Request $request ){
            $this->session = $request->session;
        }
    }

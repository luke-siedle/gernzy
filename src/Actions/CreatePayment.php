<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Payment;

class CreatePayment
{
    public function __construct()
    {
    }

    public function create($fields)
    {
        return new Payment($fields);
    }
}

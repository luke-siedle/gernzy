<?php

namespace Lab19\Cart\Packages\PaymentProviders;

use Lab19\Cart\Models\Payment;

class EFT
{
    public $publicPaymentMethod = true;
    public $requiresManualResolution = true;
    public $asynchronousPaymentResolution = true;

    public function __construct()
    {
    }

    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function createPaymentAction()
    {
        return $this->payment;
    }

    public function doesRequireManualResolution()
    {
        return $requiresManualResolution;
    }

    public function isAsynchronousPaymentResolution()
    {
        return $asynchronousPaymentResolution;
    }
}

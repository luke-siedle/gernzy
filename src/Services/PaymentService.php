<?php

namespace Lab19\Cart\Services;

use App;
use Lab19\Cart\Models\Payment;

class PaymentService
{
    public static function createAction(Payment $payment)
    {
        $makeProvider = self::getThirdPartyProvider($payment->provider);
        $provider = new $makeProvider();
        $provider->setPayment($payment);
        $action = $provider->createPaymentAction();
        return $action;
    }

    public static function getThirdPartyProvider($provider)
    {
        $config = config('config');
        $providerClass = $config['payment_providers'][ $provider ];
        if ($providerClass && class_exists($providerClass)) {
            return App::make($providerClass);
        } else {
            throw new \Exception('Payment provider ' . $provider
                . ' does not exist. Have you installed it and added it to your configuration?');
        }
    }
}

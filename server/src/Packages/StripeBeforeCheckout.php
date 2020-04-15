<?php

namespace Gernzy\Server\Packages;

use Gernzy\Server\Classes\ActionClass;
use Gernzy\Server\Services\ActionInterface;
use Illuminate\Support\Str;

class StripeBeforeCheckout implements ActionInterface
{
    public function __construct()
    {
    }

    public function run(ActionClass $action)
    {
        $data = $action->getOriginalData();

        // Add some third party specific data
        array_push($data, [
            'coupon' => Str::random(12),
            'date' => date("Y-m-d H:i:s")
        ]);

        $action->attachData(StripeBeforeCheckout::class, $data);

        $mod = $action->getLastModifiedData();

        $action->eventPreventDefault();

        return $action;
    }
}

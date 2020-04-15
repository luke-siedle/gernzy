<?php

namespace Gernzy\Server\Packages\ExamplePackage\Actions;

use Gernzy\Server\Classes\ActionClass;
use Gernzy\Server\Services\ActionInterface;
use Illuminate\Support\Str;

class ExampleBeforeCheckout implements ActionInterface
{
    public function __construct()
    {
    }

    public function run(ActionClass $action)
    {
        $data = $action->getLastModifiedData();

        // Add some third party specific data
        array_push($data, [
            'example_token' => Str::random(12),
            'example_date' => date("Y-m-d H:i:s")
        ]);

        $action->attachData(ExampleBeforeCheckout::class, $data);

        return $action;
    }
}

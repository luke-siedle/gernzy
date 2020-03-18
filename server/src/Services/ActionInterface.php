<?php

namespace Gernzy\Server\Services;

use Gernzy\Server\Classes\ActionClass;

interface ActionInterface
{
    /**
     * Main method executed on the action
     *
     * @param string
     */
    public function run(ActionClass $action);
}

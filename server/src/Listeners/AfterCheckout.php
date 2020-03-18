<?php

namespace Gernzy\Server\Listeners;

class AfterCheckout
{
    public function testFire($arg = null)
    {
        print 'AfterCheckout event testFire()';
    }
}

<?php

namespace Racine\Security\Http\Firewall;

use Racine\Event\GetResponseEvent;

interface ListenerInterface
{
    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event);
}
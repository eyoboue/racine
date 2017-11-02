<?php

namespace Racine\Security\Http\Firewall;


interface ListenerInterface
{
    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event);
}
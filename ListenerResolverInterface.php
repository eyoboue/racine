<?php

namespace Racine;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface ListenerResolverInterface
{
    public function resolve(EventDispatcherInterface $dispatcher);
}
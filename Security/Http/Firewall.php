<?php

namespace Racine\Security\Http;

use Racine\RacineEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Racine\Event\GetResponseEvent;
use Racine\Event\FinishRequestEvent;

class Firewall implements EventSubscriberInterface
{
    private $map;
    private $dispatcher;
    
    /**
     * Constructor.
     *
     * @param FirewallMapInterface     $map        A FirewallMapInterface instance
     * @param EventDispatcherInterface $dispatcher An EventDispatcherInterface instance
     */
    public function __construct(FirewallMapInterface $map, EventDispatcherInterface $dispatcher)
    {
        $this->map = $map;
        $this->dispatcher = $dispatcher;
    }
    
    /**
     * Handles security.
     *
     * @param GetResponseEvent $event An GetResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        
        // register listeners for this firewall
        list($listeners) = $this->map->getListeners($event->getRequest());
        
        // initiate the listener chain
        foreach ($listeners as $listener) {
            $listener->handle($event);
            
            if ($event->hasResponse()) {
                break;
            }
        }
    }
    
    public function onKernelFinishRequest(FinishRequestEvent $event)
    {
        $request = $event->getRequest();
        
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            RacineEvents::REQUEST => array('onKernelRequest', 8),
            RacineEvents::FINISH_REQUEST => 'onKernelFinishRequest',
        );
    }
}
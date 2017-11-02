<?php

namespace Racine\Security\Http;


use Racine\Event\FinishRequestEvent;
use Racine\Event\GetResponseEvent;
use Racine\RacineEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Firewall implements EventSubscriberInterface
{
    /**
     * @var FirewallMapInterface
     */
    private $map;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    
    private $exceptionListeners;
    
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
        $this->exceptionListeners = new \SplObjectStorage();
    }
    
    /**
     * Handles security.
     *
     * @param GetResponseEvent $event An GetResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        
        // register listeners for this firewall
        list($listeners, $exceptionListener) = $this->map->getListeners($event->getRequest());
        if (null !== $exceptionListener) {
            $this->exceptionListeners[$event->getRequest()] = $exceptionListener;
            $exceptionListener->register($this->dispatcher);
        }
        
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
        
        if (isset($this->exceptionListeners[$request])) {
            $this->exceptionListeners[$request]->unregister($this->dispatcher);
            unset($this->exceptionListeners[$request]);
        }
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
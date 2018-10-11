<?php

namespace Racine\Security\Http\Event;

use Racine\Http\Request;
use Symfony\Component\EventDispatcher\Event;

class InteractiveLogoutEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;
    
    
    /**
     * InteractiveLoginEvent constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
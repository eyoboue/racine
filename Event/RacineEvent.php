<?php

namespace Racine\Event;


use Racine\Http\Request;
use Symfony\Component\EventDispatcher\Event;

class RacineEvent extends Event
{
    /**
     * The request the kernel is currently processing.
     *
     * @var Request
     */
    private $request;
    
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    /**
     * Returns the request the kernel is currently processing.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
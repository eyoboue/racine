<?php

namespace Racine\Security\Http\Event;

use Racine\Http\Request;
use Racine\Security\Authentication\Token\TokenInterface;
use Symfony\Component\EventDispatcher\Event;

class InteractiveLoginEvent extends Event
{
    /**
     * @var TokenInterface
     */
    protected $token;
    
    /**
     * @var Request
     */
    protected $request;
    
    
    /**
     * InteractiveLoginEvent constructor.
     * @param TokenInterface $token
     * @param Request $request
     */
    public function __construct(TokenInterface $token, Request $request)
    {
        $this->token = $token;
        $this->request = $request;
    }
    
    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    public function getAuthenticationToken()
    {
        return $this->token;
    }
}
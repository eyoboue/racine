<?php

namespace Racine\Security\Core\Event;


use Racine\Security\Core\Authentication\Token\TokenInterface;
use Racine\Security\Core\Exception\AuthenticationException;

class AuthenticationFailureEvent extends AuthenticationEvent
{
    private $authenticationException;
    
    public function __construct(TokenInterface $token, AuthenticationException $ex)
    {
        parent::__construct($token);
        
        $this->authenticationException = $ex;
    }
    
    public function getAuthenticationException()
    {
        return $this->authenticationException;
    }
}
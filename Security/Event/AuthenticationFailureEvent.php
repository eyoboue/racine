<?php
namespace Racine\Security\Event;


use Racine\Security\Authentication\Token\TokenInterface;
use Racine\Security\Exception\AuthenticationException;
use Symfony\Component\EventDispatcher\Event;

class AuthenticationFailureEvent extends Event
{
    /**
     * @var AuthenticationException
     */
    public $exception;
    
    /**
     * AuthenticationFailureEvent constructor.
     * @param AuthenticationException $exception
     */
    public function __construct(AuthenticationException $exception)
    {
        $this->exception = $exception;
    }
    
    public function getAuthenticationException()
    {
        return $this->exception;
    }
}
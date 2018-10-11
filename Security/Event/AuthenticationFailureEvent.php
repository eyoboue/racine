<?php
namespace Racine\Security\Event;


use Racine\Security\Authentication\Token\TokenInterface;
use Racine\Security\Exception\AuthenticationException;

class AuthenticationFailureEvent extends AuthenticationEvent
{
    /**
     * @var AuthenticationException
     */
    public $exception;
    
    /**
     * AuthenticationFailureEvent constructor.
     * @param TokenInterface $token
     * @param AuthenticationException $exception
     */
    public function __construct(TokenInterface $token, AuthenticationException $exception)
    {
        parent::__construct($token);
        $this->exception = $exception;
    }
    
    public function getAuthenticationException()
    {
        return $this->exception;
    }
}
<?php
namespace Racine\Security\Event;

use Racine\Security\Authentication\Token\TokenInterface;
use Symfony\Component\EventDispatcher\Event;

class AuthenticationEvent extends Event
{
    /**
     * @var TokenInterface
     */
    protected $token;
    
    /**
     * AuthenticationEvent constructor.
     * @param TokenInterface $token
     */
    public function __construct(TokenInterface $token)
    {
        $this->token = $token;
    }
    
    public function getAuthenticationToken()
    {
        return $this->token;
    }
}
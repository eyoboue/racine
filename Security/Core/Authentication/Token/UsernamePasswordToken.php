<?php

namespace Racine\Security\Core\Authentication\Token;

class UsernamePasswordToken extends AbstractToken
{
    private $credentials;
    
    public function __construct($user, $credentials, array $roles = array())
    {
        parent::__construct($roles);
        
        $this->setUser($user);
        $this->credentials = $credentials;
        
        parent::setAuthenticated(count($roles) > 0);
    }
    
    /**
     * {@inheritdoc}
     */
    public function setAuthenticated($isAuthenticated)
    {
        if ($isAuthenticated) {
            throw new \LogicException('Cannot set this token to trusted after instantiation.');
        }
        
        parent::setAuthenticated(false);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return $this->credentials;
    }
    
    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        parent::eraseCredentials();
        
        $this->credentials = null;
    }
}
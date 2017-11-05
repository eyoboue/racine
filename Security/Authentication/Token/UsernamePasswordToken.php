<?php

namespace Racine\Security\Authentication\Token;


class UsernamePasswordToken extends AbstractToken
{
    private $credentials;
    
    /**
     * Constructor.
     *
     * @param string|object            $user        The username (like a nickname, email address, etc.), or a UserInterface instance or an object implementing a __toString method
     * @param mixed                    $credentials This usually is the password of the user
     * @param (RoleInterface|string)[] $roles       An array of roles
     *
     * @throws \InvalidArgumentException
     */
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
    
    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array($this->credentials, parent::serialize()));
    }
    
    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->credentials, $parentStr) = unserialize($serialized);
        parent::unserialize($parentStr);
    }
}
<?php

namespace Racine\Security\Core\Exception;


use Racine\Security\Core\User\UserInterface;

abstract class AccountStatusException extends AuthenticationException
{
    private $user;
    
    /**
     * Get the user.
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set the user.
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }
    
    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->user,
            parent::serialize(),
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function unserialize($str)
    {
        list($this->user, $parentData) = unserialize($str);
        
        parent::unserialize($parentData);
    }
}
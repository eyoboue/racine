<?php

namespace Racine\Security\Core\Exception;


class UsernameNotFoundException extends AuthenticationException
{
    private $username;
    
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Username could not be found.';
    }
    
    /**
     * Get the username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Set the username.
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }
    
    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->username,
            parent::serialize(),
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function unserialize($str)
    {
        list($this->username, $parentData) = unserialize($str);
        
        parent::unserialize($parentData);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMessageData()
    {
        return array('{{ username }}' => $this->username);
    }
}
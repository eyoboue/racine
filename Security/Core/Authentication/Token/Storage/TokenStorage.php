<?php

namespace Racine\Security\Core\Authentication\Token\Storage;


use Racine\Security\Core\Authentication\Token\TokenInterface;

class TokenStorage implements TokenStorageInterface
{
    private $token;
    
    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setToken(TokenInterface $token = null)
    {
        $this->token = $token;
    }
}
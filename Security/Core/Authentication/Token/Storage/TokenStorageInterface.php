<?php

namespace Racine\Security\Core\Authentication\Token\Storage;


use Racine\Security\Core\Authentication\Token\TokenInterface;

interface TokenStorageInterface
{
    /**
     * Returns the current security token.
     *
     * @return TokenInterface|null A TokenInterface instance or null if no authentication information is available
     */
    public function getToken();
    
    /**
     * Sets the authentication token.
     *
     * @param TokenInterface $token A TokenInterface token, or null if no further authentication information should be stored
     */
    public function setToken(TokenInterface $token = null);
}
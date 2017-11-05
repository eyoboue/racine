<?php

namespace Racine\Security\Authentication\Provider;


use Racine\Security\Authentication\Token\TokenInterface;
use Security\Exception\AuthenticationException;

interface AuthenticationProviderInterface
{
    /**
     * Attempts to authenticate a TokenInterface object.
     *
     * @param TokenInterface $token The TokenInterface instance to authenticate
     *
     * @return TokenInterface An authenticated TokenInterface instance, never null
     *
     * @throws AuthenticationException if the authentication fails
     */
    public function authenticate(TokenInterface $token);
}
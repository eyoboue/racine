<?php

namespace Racine\Security\Core\Authentication;


use Racine\Security\Core\Authentication\Token\TokenInterface;

interface AuthenticationTrustResolverInterface
{
    /**
     * Resolves whether the passed token implementation is authenticated
     * anonymously.
     *
     * If null is passed, the method must return false.
     *
     * @param TokenInterface $token
     *
     * @return bool
     */
    public function isAnonymous(TokenInterface $token = null);
    
    /**
     * Resolves whether the passed token implementation is authenticated
     * using remember-me capabilities.
     *
     * @param TokenInterface $token
     *
     * @return bool
     */
    public function isRememberMe(TokenInterface $token = null);
    
    /**
     * Resolves whether the passed token implementation is fully authenticated.
     *
     * @param TokenInterface $token
     *
     * @return bool
     */
    public function isFullFledged(TokenInterface $token = null);
}
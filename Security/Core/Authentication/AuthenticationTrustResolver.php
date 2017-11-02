<?php

namespace Racine\Security\Core\Authentication;


use Racine\Security\Core\Authentication\Token\TokenInterface;

class AuthenticationTrustResolver implements AuthenticationTrustResolverInterface
{
    private $anonymousClass;
    private $rememberMeClass;
    
    /**
     * Constructor.
     *
     * @param string $anonymousClass
     * @param string $rememberMeClass
     */
    public function __construct($anonymousClass, $rememberMeClass)
    {
        $this->anonymousClass = $anonymousClass;
        $this->rememberMeClass = $rememberMeClass;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isAnonymous(TokenInterface $token = null)
    {
        if (null === $token) {
            return false;
        }
        
        return $token instanceof $this->anonymousClass;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isRememberMe(TokenInterface $token = null)
    {
        if (null === $token) {
            return false;
        }
        
        return $token instanceof $this->rememberMeClass;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isFullFledged(TokenInterface $token = null)
    {
        if (null === $token) {
            return false;
        }
        
        return !$this->isAnonymous($token) && !$this->isRememberMe($token);
    }
}
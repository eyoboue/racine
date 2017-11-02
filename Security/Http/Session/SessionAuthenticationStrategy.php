<?php

namespace Racine\Security\Http\Session;
use Racine\Http\Request;
use Racine\Security\Core\Authentication\Token\TokenInterface;

/**
 * The default session strategy implementation.
 *
 * Supports the following strategies:
 * NONE: the session is not changed
 * MIGRATE: the session id is updated, attributes are kept
 * INVALIDATE: the session id is updated, attributes are lost
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class SessionAuthenticationStrategy implements SessionAuthenticationStrategyInterface
{
    const NONE = 'none';
    const MIGRATE = 'migrate';
    const INVALIDATE = 'invalidate';
    
    private $strategy;
    
    public function __construct($strategy)
    {
        $this->strategy = $strategy;
    }
    
    /**
     * {@inheritdoc}
     */
    public function onAuthentication(Request $request, TokenInterface $token)
    {
        switch ($this->strategy) {
            case self::NONE:
                return;
            
            case self::MIGRATE:
                // Destroying the old session is broken in php 5.4.0 - 5.4.10
                // See php bug #63379
                $destroy = \PHP_VERSION_ID < 50400 || \PHP_VERSION_ID >= 50411;
                $request->getSession()->migrate($destroy);
                
                return;
            
            case self::INVALIDATE:
                $request->getSession()->invalidate();
                
                return;
            
            default:
                throw new \RuntimeException(sprintf('Invalid session authentication strategy "%s"', $this->strategy));
        }
    }
}
<?php

namespace Racine\Security\Core\Authentication;

use Racine\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Racine\Security\Core\Authentication\Token\TokenInterface;
use Racine\Security\Core\AuthenticationEvents;
use Racine\Security\Core\Event\AuthenticationEvent;
use Racine\Security\Core\Event\AuthenticationFailureEvent;
use Racine\Security\Core\Exception\AccountStatusException;
use Racine\Security\Core\Exception\AuthenticationException;
use Racine\Security\Core\Exception\ProviderNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AuthenticationProviderManager implements AuthenticationManagerInterface
{
    private $providers;
    private $eraseCredentials;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    
    public function __construct(array $providers, $eraseCredentials = true)
    {
        if (!$providers) {
            throw new \InvalidArgumentException('You must at least add one authentication provider.');
        }
        
        foreach ($providers as $provider) {
            if (!$provider instanceof AuthenticationProviderInterface) {
                throw new \InvalidArgumentException(sprintf('Provider "%s" must implement the AuthenticationProviderInterface.', get_class($provider)));
            }
        }
        
        $this->providers = $providers;
        $this->eraseCredentials = (bool) $eraseCredentials;
    }
    
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }
    
    public function authenticate(TokenInterface $token)
    {
        $lastException = null;
        $result = null;
    
        foreach ($this->providers as $provider) {
            if (!$provider->supports($token)) {
                continue;
            }
        
            try {
                $result = $provider->authenticate($token);
            
                if (null !== $result) {
                    break;
                }
            } catch (AccountStatusException $e) {
                $lastException = $e;
            
                break;
            } catch (AuthenticationException $e) {
                $lastException = $e;
            }
        }
    
        if (null !== $result) {
            if (true === $this->eraseCredentials) {
                $result->eraseCredentials();
            }
        
            if (null !== $this->eventDispatcher) {
                $this->eventDispatcher->dispatch(AuthenticationEvents::AUTHENTICATION_SUCCESS, new AuthenticationEvent($result));
            }
        
            return $result;
        }
    
        if (null === $lastException) {
            $lastException = new ProviderNotFoundException(sprintf('No Authentication Provider found for token of class "%s".', get_class($token)));
        }
    
        if (null !== $this->eventDispatcher) {
            $this->eventDispatcher->dispatch(AuthenticationEvents::AUTHENTICATION_FAILURE, new AuthenticationFailureEvent($token, $lastException));
        }
    
        $lastException->setToken($token);
    
        throw $lastException;
    }
    
}
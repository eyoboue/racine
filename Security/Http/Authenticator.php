<?php


namespace Racine\Security\Http;


use Racine\Config;
use Racine\Http\Request;
use Racine\Security\Authentication\Token\TokenInterface;
use Racine\Security\Authentication\Token\UsernamePasswordToken;
use Racine\Security\Http\Firewall\AuthenticationListener;
use Racine\Security\Http\Session\TokenSessionResolver;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestMatcher;

class Authenticator
{
    /**
     * @var Request
     */
    private $request;
    
    /**
     * @var TokenInterface
     */
    private $defaultToken;
    
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    
    public function __construct(Request $request, EventDispatcherInterface $dispatcher)
    {
        $this->request = $request;
        $tokenSessionResolver = new TokenSessionResolver($this->request);
        $this->defaultToken = $tokenSessionResolver->getToken();
        
        $this->dispatcher = $dispatcher;
        
    }
    
    public function watch()
    {
        $securityConfig = Config::get('security');
        if(empty($securityConfig['firewall'])){
            return;
        }
        $securityFirewallConfig = $securityConfig['firewall'];
        if(!is_array($securityFirewallConfig)) return null;
        
        if(empty($securityFirewallConfig['provider'])) return null;
        if(empty($securityFirewallConfig['pattern'])) return null;
        
        $securityProviderConfig = $securityConfig['providers'][$securityFirewallConfig['provider']];
        
        $providerReflexionClass = new \ReflectionClass($securityProviderConfig['class']);
    
        if(!$providerReflexionClass->implementsInterface('\\Racine\\Security\\Authentication\\Provider\\AuthenticationProviderInterface')){
            throw new \RuntimeException('Authentication provider must be implemente \\Racine\\Security\\Authentication\\Provider\\AuthenticationProviderInterface');
        }
        if(!$providerReflexionClass->isSubclassOf('\\Racine\\Security\\Authentication\\Provider\\UserAuthenticationProvider')){
            throw new \RuntimeException('Authentication provider must be subclass of \\Racine\\Security\\Authentication\\Provider\\UserAuthenticationProvider');
        }
        $provider = $providerReflexionClass->newInstance($securityFirewallConfig['provider']);
        
        $options = [];
        if(!empty($securityFirewallConfig['form_login']) && is_array($securityFirewallConfig['form_login'])){
            $options = array_merge($securityFirewallConfig['form_login'], $options);
        }
        
        $authListener = new AuthenticationListener($this->defaultToken, $provider, $securityFirewallConfig['provider'], $options, $this->dispatcher);
        $listeners[] = $authListener;
    
        $firewallMap = new FirewallMap();
        $firewallMap->add(new RequestMatcher($securityFirewallConfig['pattern']), $listeners);
        
        $firewall = new Firewall($firewallMap, $this->dispatcher);
        
        $this->dispatcher->addSubscriber($firewall);
        
    }
}
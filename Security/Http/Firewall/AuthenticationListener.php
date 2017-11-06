<?php

namespace Racine\Security\Http\Firewall;


use Racine\Http\Request;
use Racine\Security\Authentication\Provider\AuthenticationProviderInterface;
use Racine\Security\Authentication\Token\TokenInterface;
use Security\Exception\AuthenticationException;
use Security\Exception\BadCredentialsException;
use Security\Exception\SessionUnavailableException;
use Security\Exception\UsernameNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Racine\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthenticationListener implements ListenerInterface
{
    /**
     * @var array
     */
    protected $options;
    
    /**
     * @var AuthenticationProviderInterface
     */
    protected $authenticationManager;
    
    /**
     * @var string
     */
    protected $providerKey;
    
    /**
     * @var TokenInterface
     */
    private $token;
    
    /**
     * @var EventDispatcherInterface|null
     */
    private $dispatcher;
    
    public function __construct(TokenInterface $token = null, AuthenticationProviderInterface $authenticationProvider, $providerKey, array $options = [], EventDispatcherInterface $dispatcher = null)
    {
        $this->token = $token;
        $this->authenticationManager = $authenticationProvider;
        $this->providerKey = $providerKey;
        $this->options = array_merge(array(
            'check_path' => '/login.php',
            'login_path' => '/login.php',
            'always_use_default_target_path' => false,
            'default_target_path' => '/',
            'target_path_parameter' => '_target_path',
            'use_referer' => false,
            'failure_path' => null,
            'failure_forward' => false,
            'require_previous_session' => true,
        ), $options);
        $this->dispatcher = $dispatcher;
    }
    
    /**
     * Handles form based authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     *
     * @throws \RuntimeException
     * @throws SessionUnavailableException
     */
    final public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        
        if(internal_request_uri($request->getRequestUri()) == $this->options['login_path'] || internal_request_uri($request->getRequestUri()) == $this->options['check_path']){
            return;
        }
        
        $response = null;
        
        if (!$request->hasSession()) {
            throw new \RuntimeException('This authentication method requires a session.');
        }
        
        try {
            if ($this->options['require_previous_session'] && !$request->hasPreviousSession()) {
                throw new SessionUnavailableException('Your session has timed out, or you have disabled cookies.');
            }
            
            if (!is_null($this->token)){
                $this->token = $this->authenticationManager->authenticate($this->token);
            }
           
        }catch (AuthenticationException $e) {
            $this->onFailure($request, $e);
            if($e instanceof UsernameNotFoundException || $e instanceof BadCredentialsException){
                $response = new RedirectResponse(path($this->options['login_path']));
            }
            
        }
        
        if(is_null($this->token)){
            $response = new RedirectResponse(path($this->options['login_path']));
        }
        
        if($response instanceof RedirectResponse){
            $response->send();
        }
    }
    
    private function onFailure(Request $request, AuthenticationException $failed)
    {
    
    }
    
    private function onSuccess(Request $request, TokenInterface $token)
    {
    
    }
}
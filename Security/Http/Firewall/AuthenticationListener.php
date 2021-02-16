<?php

namespace Racine\Security\Http\Firewall;


use Racine\Application;
use Racine\Http\Controller;
use Racine\Http\Request;
use Racine\Security\Authentication\Provider\AuthenticationProviderInterface;
use Racine\Security\Authentication\Token\TokenInterface;
use Racine\Security\AuthenticationEvents;
use Racine\Security\Event\AuthenticationEvent;
use Racine\Security\Event\AuthenticationFailureEvent;
use Racine\Security\Exception\AuthenticationException;
use Racine\Security\Exception\BadCredentialsException;
use Racine\Security\Exception\SessionUnavailableException;
use Racine\Security\Exception\UsernameNotFoundException;
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
    
    /**
     * @var Application
     */
    private $application;
    
    public function __construct(Application &$application, TokenInterface $token = null, AuthenticationProviderInterface $authenticationProvider, $providerKey, array $options = [], EventDispatcherInterface $dispatcher = null)
    {
        $this->application = $application;
        
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
        if(preg_match('/('.addcslashes($this->options['login_path'], './').')$/', internal_request_uri($request->getRequestUri())) || preg_match('/('.addcslashes($this->options['check_path'], './').')$/', internal_request_uri($request->getRequestUri()))){
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
                $token = $this->authenticationManager->authenticate($this->token);
                if($token instanceof TokenInterface){
                    $this->token = $token;
                    $this->application->setToken($token);
                    $this->onSuccess($request, $this->token);
                }
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
            die($response->send());
        }
    }
    
    private function onFailure(Request $request, AuthenticationException $failed)
    {
        $this->application->getDispatcher()->dispatch(AuthenticationEvents::AUTHENTICATION_FAILURE, new AuthenticationFailureEvent($failed));
    }
    
    private function onSuccess(Request $request, TokenInterface $token)
    {
        $this->application->getDispatcher()->dispatch(AuthenticationEvents::AUTHENTICATION_SUCCESS, new AuthenticationEvent($token));
    }
}
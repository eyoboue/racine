<?php

namespace Racine\Security\Http\Firewall;


use Racine\Http\Request;
use Racine\Security\Core\Authentication\AuthenticationManagerInterface;
use Racine\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Racine\Security\Core\Authentication\Token\UsernamePasswordToken;
use Racine\Security\Core\Exception\BadCredentialsException;
use Racine\Security\Core\Security;
use Racine\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UsernamePasswordFormAuthenticationListener extends AbstractAuthenticationListener
{
    private $csrfTokenManager;
    
    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null, $csrfTokenManager = null)
    {
        
        parent::__construct($tokenStorage, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $successHandler, $failureHandler, array_merge(array(
            'username_parameter' => '_username',
            'password_parameter' => '_password',
            'csrf_parameter' => '_csrf_token',
            'csrf_token_id' => 'authenticate',
            'post_only' => true,
        ), $options), $logger, $dispatcher);
        
        $this->csrfTokenManager = $csrfTokenManager;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function requiresAuthentication(Request $request)
    {
        if ($this->options['post_only'] && !$request->isMethod('POST')) {
            return false;
        }
        
        return parent::requiresAuthentication($request);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        if (null !== $this->csrfTokenManager) {
            $csrfToken = ParameterBagUtils::getRequestParameterValue($request, $this->options['csrf_parameter']);
            
            if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken($this->options['csrf_token_id'], $csrfToken))) {
                throw new InvalidCsrfTokenException('Invalid CSRF token.');
            }
        }
        
        if ($this->options['post_only']) {
            $username = trim(ParameterBagUtils::getParameterBagValue($request->request, $this->options['username_parameter']));
            $password = ParameterBagUtils::getParameterBagValue($request->request, $this->options['password_parameter']);
        } else {
            $username = trim(ParameterBagUtils::getRequestParameterValue($request, $this->options['username_parameter']));
            $password = ParameterBagUtils::getRequestParameterValue($request, $this->options['password_parameter']);
        }
        
        if (strlen($username) > Security::MAX_USERNAME_LENGTH) {
            throw new BadCredentialsException('Invalid username.');
        }
        
        $request->getSession()->set(Security::LAST_USERNAME, $username);
        
        return $this->authenticationManager->authenticate(new UsernamePasswordToken($username, $password, $this->providerKey));
    }
}
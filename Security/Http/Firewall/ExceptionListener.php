<?php

namespace Racine\Security\Http\Firewall;


use Racine\Http\Request;
use Racine\Http\Response;
use Racine\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Racine\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Racine\Security\Core\Exception\AccountStatusException;
use Racine\Security\Core\Exception\AuthenticationException;
use Racine\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExceptionListener
{
    private $tokenStorage;
    private $providerKey;
    private $accessDeniedHandler;
    private $authenticationEntryPoint;
    private $authenticationTrustResolver;
    private $errorPage;
    private $logger;
    private $httpUtils;
    private $stateless;
    
    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationTrustResolverInterface $trustResolver, HttpUtils $httpUtils, $providerKey, AuthenticationEntryPointInterfacee $authenticationEntryPoint = null, $errorPage = null, AccessDeniedHandlerInterface $accessDeniedHandler = null, LoggerInterface $logger = null, $stateless = false)
    {
        $this->tokenStorage = $tokenStorage;
        $this->accessDeniedHandler = $accessDeniedHandler;
        $this->httpUtils = $httpUtils;
        $this->providerKey = $providerKey;
        $this->authenticationEntryPoint = $authenticationEntryPoint;
        $this->authenticationTrustResolver = $trustResolver;
        $this->errorPage = $errorPage;
        $this->logger = $logger;
        $this->stateless = $stateless;
    }
    
    /**
     * Registers a onKernelException listener to take care of security exceptions.
     *
     * @param EventDispatcherInterface $dispatcher An EventDispatcherInterface instance
     */
    public function register(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addListener(KernelEvents::EXCEPTION, array($this, 'onKernelException'));
    }
    
    /**
     * Unregisters the dispatcher.
     *
     * @param EventDispatcherInterface $dispatcher An EventDispatcherInterface instance
     */
    public function unregister(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->removeListener(KernelEvents::EXCEPTION, array($this, 'onKernelException'));
    }
    
    /**
     * Handles security related exceptions.
     *
     * @param GetResponseForExceptionEvent $event An GetResponseForExceptionEvent instance
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        do {
            if ($exception instanceof AuthenticationException) {
                return $this->handleAuthenticationException($event, $exception);
            } elseif ($exception instanceof AccessDeniedException) {
                return $this->handleAccessDeniedException($event, $exception);
            } elseif ($exception instanceof LogoutException) {
                return $this->handleLogoutException($exception);
            }
        } while (null !== $exception = $exception->getPrevious());
    }
    
    private function handleAuthenticationException(GetResponseForExceptionEvent $event, AuthenticationException $exception)
    {
        if (null !== $this->logger) {
            $this->logger->info('An AuthenticationException was thrown; redirecting to authentication entry point.', array('exception' => $exception));
        }
        
        try {
            $event->setResponse($this->startAuthentication($event->getRequest(), $exception));
        } catch (\Exception $e) {
            $event->setException($e);
        }
    }
    
    private function handleAccessDeniedException(GetResponseForExceptionEvent $event, AccessDeniedException $exception)
    {
        $event->setException(new AccessDeniedHttpException($exception->getMessage(), $exception));
        
        $token = $this->tokenStorage->getToken();
        if (!$this->authenticationTrustResolver->isFullFledged($token)) {
            if (null !== $this->logger) {
                $this->logger->debug('Access denied, the user is not fully authenticated; redirecting to authentication entry point.', array('exception' => $exception));
            }
            
            try {
                $insufficientAuthenticationException = new InsufficientAuthenticationException('Full authentication is required to access this resource.', 0, $exception);
                $insufficientAuthenticationException->setToken($token);
                
                $event->setResponse($this->startAuthentication($event->getRequest(), $insufficientAuthenticationException));
            } catch (\Exception $e) {
                $event->setException($e);
            }
            
            return;
        }
        
        if (null !== $this->logger) {
            $this->logger->debug('Access denied, the user is neither anonymous, nor remember-me.', array('exception' => $exception));
        }
        
        try {
            if (null !== $this->accessDeniedHandler) {
                $response = $this->accessDeniedHandler->handle($event->getRequest(), $exception);
                
                if ($response instanceof Response) {
                    $event->setResponse($response);
                }
            } elseif (null !== $this->errorPage) {
                $subRequest = $this->httpUtils->createRequest($event->getRequest(), $this->errorPage);
                $subRequest->attributes->set(Security::ACCESS_DENIED_ERROR, $exception);
                
                $event->setResponse($event->getKernel()->handle($subRequest, HttpKernelInterface::SUB_REQUEST, true));
            }
        } catch (\Exception $e) {
            if (null !== $this->logger) {
                $this->logger->error('An exception was thrown when handling an AccessDeniedException.', array('exception' => $e));
            }
            
            $event->setException(new \RuntimeException('Exception thrown when handling an exception.', 0, $e));
        }
    }
    
    private function handleLogoutException(LogoutException $exception)
    {
        if (null !== $this->logger) {
            $this->logger->info('A LogoutException was thrown.', array('exception' => $exception));
        }
    }
    
    /**
     * @param Request                 $request
     * @param AuthenticationException $authException
     *
     * @return Response
     *
     * @throws AuthenticationException
     */
    private function startAuthentication(Request $request, AuthenticationException $authException)
    {
        if (null === $this->authenticationEntryPoint) {
            throw $authException;
        }
        
        if (null !== $this->logger) {
            $this->logger->debug('Calling Authentication entry point.');
        }
        
        if (!$this->stateless) {
            $this->setTargetPath($request);
        }
        
        if ($authException instanceof AccountStatusException) {
            // remove the security token to prevent infinite redirect loops
            $this->tokenStorage->setToken(null);
            
            if (null !== $this->logger) {
                $this->logger->info('The security token was removed due to an AccountStatusException.', array('exception' => $authException));
            }
        }
        
        return $this->authenticationEntryPoint->start($request, $authException);
    }
    
    /**
     * @param Request $request
     */
    protected function setTargetPath(Request $request)
    {
        // session isn't required when using HTTP basic authentication mechanism for example
        if ($request->hasSession() && $request->isMethodSafe(false) && !$request->isXmlHttpRequest()) {
            $request->getSession()->set('_security.'.$this->providerKey.'.target_path', $request->getUri());
        }
    }
}
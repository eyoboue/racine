<?php

namespace Racine\Security\Http\EntryPoint;


use Racine\Http\Request;
use Racine\Security\Core\Exception\AuthenticationException;

class FormAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private $loginPath;
    private $useForward;
    
    /**
     * Constructor.
     *
     * @param string              $loginPath  The path to the login form
     */
    public function __construct($loginPath, $useForward = false)
    {
        $this->loginPath = $loginPath;
        $this->useForward = (bool) $useForward;
    }
    
    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        /*if ($this->useForward) {
            $subRequest = $this->httpUtils->createRequest($request, $this->loginPath);
            
            $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            if (200 === $response->getStatusCode()) {
                $response->headers->set('X-Status-Code', 401);
            }
            
            return $response;
        }*/
        
//        return $this->httpUtils->createRedirectResponse($request, $this->loginPath);
        return null;
    }
}
<?php


namespace Racine\Http;


use Racine\Application;
use Racine\Logger\Logger;
use Racine\Security\Authentication\Token\TokenInterface;
use Racine\Security\User\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Templating\PhpEngine;

abstract class Controller
{
    /**
     * @var Application
     */
    protected $app;
    
    /**
     * @var Request
     */
    protected $request;
    
    /**
     * @var PhpEngine
     */
    protected $templating;
    
    
    /**
     * @var EventDispatcher
     */
    protected $dispatcher;
    
    /**
     * @var TokenInterface
     */
    protected $token;
    
    /**
     * @var UserInterface
     */
    protected $user;
    
    /**
     * @var Logger
     */
    protected $logger;
    
    /**
     * @var array
     */
    protected $errors = [];
    
    /**
     * @param Application $app
     */
    public function setApp(Application &$app)
    {
        $this->app = $app;
        
        $this->request = $this->app->getRequest();
        $this->dispatcher = $this->app->getDispatcher();
        $this->templating = $this->app->getTemplating();
        $this->logger = $this->app->getLogger();
        
        $this->token = $this->app->getToken();
        if($this->token instanceof TokenInterface){
            $this->user = $this->token->getUser();
        }
    }
    
    
    protected function render($view, array $params = [], $responseCode = Response::HTTP_OK, $headers = [])
    {
        return $this->app->render($view, $params, $responseCode, $headers);
    }
    
    protected function dispatch($name, $event){
        $this->dispatcher->dispatch($name, $event);
    }
    
    protected function authorize($action, $payload = null)
    {
        $this->app->authorize($action, $payload);
    }
}
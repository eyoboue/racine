<?php


namespace Racine;

use Racine\Event\FinishRequestEvent;
use Racine\Event\GetResponseEvent;
use Racine\Http\Controller as BaseController;
use Racine\Http\Request;
use Racine\Http\Response;
use Racine\Http\Session;
use Racine\Security\Authentication\Token\TokenInterface;
use Racine\Security\Http\Authenticator;
use Racine\Security\Http\Session\TokenSessionResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;

class Application
{
    const DEFAULT_CONTROLLER_ACTION = 'index';
    const SESSION_NAME = 'racine_session';
    
    /**
     * @var TokenInterface
     */
    private $token;
    
    /**
     * @var Application
     */
    private static $instance = null;
    
    /**
     * @var Request
     */
    private $request;
    
    /**
     * @var PhpEngine
     */
    private $templating;
    
    /**
     * @var BaseController
     */
    private $currentController;
    
    /**
     * @var \ActiveRecord\Config
     */
    private  $dbCfg;
    
    /**
     * @var EventDispatcher
     */
    private $dispatcher;
    
    /**
     * @var Logger
     */
    private $logger;
    
    /**
     * @var string|null
     */
    private $action = null;
    
    private function __construct()
    {
        $this->initialize();
    }
    
    private function initialize()
    {
        $this->logger = new Logger();
        
        $this->request = Request::createFromGlobals();
        $this->initSession();
    
        $this->initDB();
    
        $this->iniTemplating();
    
        $this->configureDispatcher();
        $this->securityHandle();
    
        $this->dispatcher->dispatch(RacineEvents::REQUEST, new GetResponseEvent($this->request));
    }
    
    private function initSession()
    {
        $session = new Session();
        
        if(!$session->isStarted()){
            $session->setName(self::SESSION_NAME);
            $session->start();
        }
        $this->request->setSession($session);
    }
    
    private function configureDispatcher()
    {
        $this->dispatcher = new EventDispatcher();
        $this->setListeners();
    }
    
    private function securityHandle()
    {
        $authenticator = new Authenticator($this->request, $this->dispatcher);
        $authenticator->watch($this);
    }
    
    private function setListeners()
    {
        $listenerResolver = new ListenerResolver();
        $listenerResolver->resolve($this->dispatcher);
    }
    
    private function iniTemplating()
    {
        $loader = new FilesystemLoader([Config::getViewsDir().DIRECTORY_SEPARATOR.'%name%']);
    
        $this->templating = new PhpEngine(new TemplateNameParser(), $loader);
        $this->templating->set(new SlotsHelper());
    }
    
    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function run($controllerClass = null, $action = null)
    {
        $this->action = $action;
        if(is_callable($controllerClass)){
            $response = $controllerClass($this);
        }else{
            $response = $this->handle($controllerClass, $action);
        }
    
        self::send($response);
        $this->terminate($response);
    }
    
    private static function send($response)
    {
        if(is_array($response)){
            dump($response);
        }elseif(is_object($response)){
            if(($response instanceof \Symfony\Component\HttpFoundation\Response) || is_subclass_of($response, "\\Symfony\\Component\\HttpFoundation\\Response")){
                $response->send();
            }
        }else{
            if(!is_null($response)){
                echo $response;
            }
            
        }
    }
    
    private function handle($controllerClass, $action = null)
    {
        if(empty($action)){
            if($this->request->query->has(_CONTROLLER_REQUEST_ACTION_)){
                $action = $this->request->query->get(_CONTROLLER_REQUEST_ACTION_);
            }else{
                $action = self::DEFAULT_CONTROLLER_ACTION;
            }
        }
        try{
            return $this->resolveController($controllerClass, $action);
        }catch (\Exception $e){
            return new Response($e->getMessage()."<br>File: ".$e->getFile()."<br> Line: ".$e->getLine(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    private function resolveController($controllerClass, $action)
    {
        $controllerReflexionClass = new \ReflectionClass($controllerClass);
        
        if(!$controllerReflexionClass->isSubclassOf('\\Racine\\Http\\Controller')){
            throw new \Exception($controllerClass.' must be subclass of "App\\Http\\Controller"');
        }
        
        if(is_null($this->action)){
            switch(strtolower($this->request->getMethod())){
                case 'post':
                    $action = 'add';
                    break;
                case 'put':
                    $action = 'edit';
                    break;
                case 'delete':
                    $action = 'delete';
                    break;
                default:
                    $action = self::DEFAULT_CONTROLLER_ACTION;
            }
            if(!$controllerReflexionClass->hasMethod($action)){
                throw new \BadMethodCallException($controllerClass." doesn't have '".$action."' method");
            }
            
        }
        
        $this->currentController = new $controllerClass();
        
        $this->currentController->setRequest($this->request);
        $this->currentController->setTemplating($this->templating);
        $this->currentController->setDispatcher($this->dispatcher);
        if($this->getToken() instanceof TokenInterface){
            $this->currentController->setToken($this->getToken());
        }
        
        $method = new \ReflectionMethod($controllerClass, $action);
        return $method->invoke($this->currentController);
    }
    
    private function initDB()
    {
        $this->dbCfg = \ActiveRecord\Config::instance();
        $this->dbCfg->set_model_directory(Config::getAppDir().DIRECTORY_SEPARATOR.'Models');
        if(isset(Config::get('database', true)->connections) && ($dbConfigs = Config::get('database', true))){
            
            $dbConfig = $dbConfigs->connections->{$dbConfigs->default};
            $connections = [
                'default' => $dbConfig->driver.'://'.$dbConfig->user.':'.$dbConfig->password.'@'.$dbConfig->host.':'.$dbConfig->port
                    .'/'.$dbConfig->database.'?charset='.$dbConfig->charset
            ];
            $this->dbCfg->set_connections($connections, 'default');
        }
        
        
        
    }
    
    private function terminate($response)
    {
        $this->dispatcher->dispatch(RacineEvents::TERMINATE, new FinishRequestEvent($this->request));
    }
    
    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * @return PhpEngine
     */
    public function getTemplating()
    {
        return $this->templating;
    }
    
    public function render($view, array $params = [])
    {
        return new Response($this->templating->render($view, $params));
    }
    
    public static function templating()
    {
        return self::getInstance()->getTemplating();
    }
    
    /**
     * @param TokenInterface $token
     * @return self
     */
    public function setToken(TokenInterface $token)
    {
        $this->token = $token;
        return $this;
    }
    
    
    
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
    
    /**
     * @return BaseController
     */
    public function getCurrentController()
    {
        return $this->currentController;
    }
}
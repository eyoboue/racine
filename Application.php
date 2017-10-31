<?php


namespace Racine;


use App\Events\Core\AppInitEvent;
use App\Events\Core\AppRequestEvent;
use App\Events\Core\AppTerminateEvent;
use App\Events\Core\CoreEvents;
use Racine\Http\Controller as BaseController;
use Racine\Http\Request;
use Racine\Http\Response;
use Racine\Http\Session;
use Racine\Subscribers\Core\AuthSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Racine\Config;

class Application
{
    const DEFAULT_CONTROLLER_ACTION = 'index';
    
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
    
    private function __construct()
    {
        $this->initialize();
    }
    
    private function initialize()
    {
        $this->request = Request::createFromGlobals();
        $this->initSession();
        $this->initDB();
        $this->iniTemplating();
        $this->configureDispatcher();
        
        $this->dispatcher->dispatch(CoreEvents::INIT, new AppInitEvent($this));
    }
    
    private function initSession()
    {
        $session = new Session();
        if(!$session->isStarted()){
            $session->setName('racine_session');
            $session->start();
        }
        $this->request->setSession($session);
    }
    
    private function configureDispatcher()
    {
        $this->dispatcher = new EventDispatcher();
        $this->setListeners();
        $this->setSubscribers();
    }
    
    private function setListeners()
    {
        /*$this->dispatcher->addListener(CoreEvents::INIT, function (AppInitEvent $event){
        
        });*/
    
    }
    
    private function setSubscribers()
    {
        $this->dispatcher->addSubscriber(new AuthSubscriber());
    }
    
    private function iniTemplating()
    {
        $loader = new FilesystemLoader(Config::getViewsDir().DIRECTORY_SEPARATOR.'%name%');
    
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
        $this->dispatcher->dispatch(CoreEvents::REQUEST, new AppRequestEvent($this->request));
        
        $response = $this->handle($controllerClass, $action);
        
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
            if($this->request->query->has('_a')){
                $action = $this->request->query->get('_a');
            }else{
                switch(strtolower($this->request->getMethod())){
                    case 'delete':
                        $action = 'delete';
                        break;
                    default:
                        $action = self::DEFAULT_CONTROLLER_ACTION;
                }
            
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
        
        if(!$controllerReflexionClass->isSubclassOf('App\\Http\\Controller')){
            throw new \Exception($controllerClass.' must be subclass of "App\\Http\\Controller"');
        }
        
        if(!$controllerReflexionClass->hasMethod($action)){
            throw new \BadMethodCallException($controllerClass." doesn't have '".$action."' method");
        }
        
        $this->currentController = new $controllerClass();
        
        $this->currentController->setRequest($this->request);
        $this->currentController->setTemplating($this->templating);
        $this->currentController->setDispatcher($this->dispatcher);
        
        $method = new \ReflectionMethod($controllerClass, $action);
        return $method->invoke($this->currentController);
    }
    
    private function initDB()
    {
        $this->dbCfg = \ActiveRecord\Config::instance();
        $this->dbCfg->set_model_directory(Config::getAppDir().DIRECTORY_SEPARATOR.'Models');
        $dbConfig = Config::get('app')->db;
        $connections = [
            'default' => $dbConfig->driver.'://'.$dbConfig->user.':'.$dbConfig->pass.'@'.$dbConfig->host.':'.$dbConfig->port
                .'/'.$dbConfig->name.'?charset='.$dbConfig->charset
        ];
        $this->dbCfg->set_connections($connections, 'default');
        
    }
    
    private function terminate($response)
    {
        $this->dispatcher->dispatch(CoreEvents::TERMINATE, new AppTerminateEvent($this->request, $response));
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
    
    public static function templating()
    {
        return self::getInstance()->getTemplating();
    }
    
}
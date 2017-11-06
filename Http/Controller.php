<?php


namespace Racine\Http;


use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Templating\PhpEngine;

abstract class Controller
{
    /**
     * @var Request
     */
    protected $request;
    
    /**
     * @var PhpEngine
     */
    private $templating;
    
    
    /**
     * @var EventDispatcher
     */
    private $dispatcher;
    
    /**
     * @var array
     */
    protected $errors = [];
    
    
    protected function render($view, array $params = [])
    {
        return new Response($this->templating->render($view, $params));
    }
    
    protected function dispatch($name, $event){
        $this->dispatcher->dispatch($name, $event);
    }
    
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
    
    public function setTemplating(PhpEngine $templating)
    {
        $this->templating = $templating;
    }
    
    /**
     * @param EventDispatcher $dispatcher
     */
    public function setDispatcher(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
}
<?php
namespace Racine;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ListenerResolver implements ListenerResolverInterface
{
    /**
     * @var array
     */
    private $listeners;
    
    /**
     * @var array
     */
    private $sources;
    
    public function __construct()
    {
        $this->sources = [
            realpath(__DIR__.'/listeners.php'),
            _APP_DIR_.'/listeners.php',
        ];
        
        $this->listeners = $this->load();
        
    }
    
    public function resolve(EventDispatcherInterface $dispatcher)
    {
        
        foreach ($this->listeners as $name => $listener){
            if(!empty($listener['class']) && !empty($listener['type'])){
                switch ($listener['type']){
                    case 'racine.event_listener':
                        if(!empty($listener['method']) && !empty($listener['event'])){
                            if(!empty($listener['priority'])){
                                $listener['priority'] = 0;
                            }
                            $dispatcher->addListener($listener['event'], [new $listener['class'](), $listener['method']], (int)$listener['priority']);
                        }
                        break;
                    case 'racine.event_subscriber':
                        $dispatcher->addSubscriber(new $listener['class']());
                        break;
                }
            }
        }
        
        return $dispatcher;
    }
    
    
    private function load()
    {
        $this->listeners = [];
        
        foreach ($this->sources as $source){
            if(file_exists($source)){
                $this->listeners = array_merge_recursive($this->listeners, (require $source));
            }
        }
        
        return $this->listeners;
    }
    
}
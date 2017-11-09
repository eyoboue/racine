<?php

namespace Racine\Logger;

class Logger extends \Monolog\Logger
{
    public function __construct($processors = array())
    {
        parent::__construct('racine', [new MyStreamHandler(_STORAGE_DIR_.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'racine.log')], $processors);
    }
    
    /**
     * @param int $level
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function addRecord($level, $message, array $context = array())
    {
        // Ignoring Exceptions
        try{
            return parent::addRecord($level, $message, $context);
        }catch (\Exception $exception){
            return false;
        }
        
    }
    
    
}
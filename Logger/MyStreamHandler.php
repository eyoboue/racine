<?php

namespace Racine\Logger;


use Monolog\Handler\StreamHandler;

class MyStreamHandler extends StreamHandler
{
    protected function write(array $record)
    {
        try {
            parent::write($record);
        }catch (\Exception $exception){
            // TODO: Ignoring exceptions
        }
        
    }
    
}
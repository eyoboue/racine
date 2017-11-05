<?php

namespace Racine;


use Monolog\Handler\StreamHandler;

class Logger extends \Monolog\Logger
{
    public function __construct($processors = array())
    {
        parent::__construct('racine', [new StreamHandler(_STORAGE_DIR_.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'racine.log')], $processors);
    }
}
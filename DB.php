<?php

namespace Racine;


use ActiveRecord\ConnectionManager;

class DB
{
    private function __construct()
    {
    }
    
    public static function connect($name = null)
    {
        return ConnectionManager::get_connection($name);
    }
    
    public static function disconnect($name = null)
    {
        ConnectionManager::drop_connection($name);
    }
}
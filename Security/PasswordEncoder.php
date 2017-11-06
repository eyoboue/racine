<?php

namespace Racine\Security;


class PasswordEncoder
{
    private function __construct()
    {
    }
    
    public static function encode($password)
    {
        return hash('sha256', $password);
    }
}
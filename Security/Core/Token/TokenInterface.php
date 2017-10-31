<?php

namespace Racine\Security\Core\Token;

interface TokenInterface extends \Serializable
{
    public function getCredentials();
    
    public function getUser();
}
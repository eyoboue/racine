<?php

namespace Racine\Security\Core\Authentication;

use Racine\Security\Core\Authentication\Token\TokenInterface;

interface AuthenticationManagerInterface 
{
    public function authenticate(TokenInterface $token);
}
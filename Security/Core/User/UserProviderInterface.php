<?php

namespace Racine\Security\Core\User;

interface UserProviderInterface
{
    public function loadUserByUsername($username);
    
    public function supportsClass($class);
}
<?php

namespace Racine\Security\Core\Authentication\Provider;

use Racine\Security\Core\Authentication\Token\UsernamePasswordToken;
use Racine\Security\Core\User\UserInterface;

class DaoAuthenticationProvider extends UserAuthenticationProvider
{
    protected function retrieveUser($username, UsernamePasswordToken $token)
    {
        // TODO: Implement retrieveUser() method.
    }
    
    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        // TODO: Implement checkAuthentication() method.
    }
    
}

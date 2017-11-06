<?php

namespace Racine\Security;

use Racine\Http\Request;
use Racine\Security\User\UserInterface;
use Racine\Security\Authentication\Token\UsernamePasswordToken;

class Authentication
{
    public static function login(Request $request, UserInterface $user)
    {
        if(is_null($user)) return false;
        
        $token = new UsernamePasswordToken($user, $user->getPassword(), ['user']);
        
        $session = $request->getSession();
        $session->set(Security::LOGGED_TOKEN, serialize($token));
        
        
        return $token->isAuthenticated();
    }
    
    public static function logout(Request $request)
    {
        return $request->getSession()->invalidate();
    }
}
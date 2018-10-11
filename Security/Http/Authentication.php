<?php

namespace Racine\Security\Http;

use Racine\Http\Request;
use Racine\Security\Http\Event\InteractiveLoginEvent;
use Racine\Security\Http\Event\InteractiveLoginFailureEvent;
use Racine\Security\Http\Event\InteractiveLogoutEvent;
use Racine\Security\Security;
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
        
        $isAuthentication = $token->isAuthenticated();
        if($isAuthentication){
            event(SecurityEvents::INTERACTIVE_LOGIN, new InteractiveLoginEvent($token, $request));
        }else{
            event(SecurityEvents::INTERACTIVE_LOGIN_FAILURE, new InteractiveLoginFailureEvent($token, $request));
        }
        return $isAuthentication;
    }
    
    public static function logout(Request $request)
    {
        $logout = $request->getSession()->invalidate();
        event(SecurityEvents::INTERACTIVE_LOGOUT, new InteractiveLogoutEvent($request));
        return $logout;
    }
}
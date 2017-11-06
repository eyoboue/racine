<?php

namespace Racine\Security\Authentication\Provider;


use Racine\Config;
use Racine\Security\Authentication\Token\UsernamePasswordToken;
use Racine\Security\User\UserInterface;
use Racine\Security\Exception\BadCredentialsException;

class DaoAuthenticationProvider extends UserAuthenticationProvider
{
    protected function retrieveUser($username, UsernamePasswordToken $token)
    {
        $providerConfig = Config::get('security')['providers'][$this->getProviderKey()];
        $userClass = $providerConfig['entity']['class'];
        $user = null;
        $reflexionUserClass = new \ReflectionClass($userClass);
        if ($reflexionUserClass->isSubclassOf('\\Racine\\Model') && $reflexionUserClass->implementsInterface('\\Racine\\Security\\User\\UserInterface')){
            $userLoadMethod = new \ReflectionMethod($userClass, 'first');
            $user = $userLoadMethod->invoke(null, [
                'conditions' => [$providerConfig['entity']['property'].' = ?', [$username]]
            ]);
        }
        
        return $user;
    }
    
    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        if(!($user->getUsername() == $token->getUsername())){
            throw new BadCredentialsException();
        }
    }
}
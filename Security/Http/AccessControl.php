<?php


namespace Racine\Security\Http;


use Racine\Application;
use Racine\Security\Authorization\AuthorizationChecker;
use Symfony\Component\HttpFoundation\RequestMatcher;

class AccessControl
{
    private function __construct()
    {
    }
    
    public static function check(Application &$application)
    {
        $securityConf = config('security');
        if(!isset($securityConf['access_control'])) return true;
        if(!is_array($securityConf['access_control'])) return true;
        
        if (is_null($application->getToken())) return true;
        if(is_null($application->getToken()->getUser())) return true;
    
        foreach ($securityConf['access_control'] as $access){
            if(!is_array($access)) continue;
            if(!isset($access['path']) || !isset($access['can'])) continue;
            $matcher = new RequestMatcher($access['path']);
            if(!$matcher->matches($application->getRequest())) continue;
    
            $ability = explode(',', $access['can']);
            if(!empty($ability[0])){
                $action = $ability[0];
                $payload = null;
                if(!empty($ability[1])){
                    $payload = $ability[1];
                }
                return AuthorizationChecker::getInstance()->check($application->getToken()->getUser(), $action, $payload);
            }
            
        }
        
        return true;
    }
}
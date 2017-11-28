<?php


namespace Racine\Security\Authorization;


use Racine\Model;
use Racine\Security\User\UserInterface;

class AuthorizationChecker
{
    private static $instance = null;
    private $policies = [];
    
    private function __construct()
    {
        $this->policies = require _APP_DIR_.'/policies.php';
    }
    
    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public function check(UserInterface $user, $action, $payload = null)
    {
        return $this->applyPolicies($user, $action, $payload, $this->getMatchedPolicies($action, $payload));
    }
    
    private function getMatchedPolicies($action, $payload = null)
    {
        $policies = [];
        foreach ($this->policies as $model => $policy) {
            if(!class_exists($policy)) continue;
            if(!method_exists($policy, $action)) continue;
    
            if(class_exists($model)){
                if (($payload instanceof Model) && (get_class($payload) === $model)) {
                    $policies[] = $policy;
                } elseif ( is_string($payload) && class_exists($payload) && $payload === $model) {
                    $policies[] = $policy;
                }
            } else {
                $policies[] = $policy;
            }
        }
        
        return $policies;
    }
    
    private function applyPolicies(UserInterface $user, $action, $payload = null, array $policies = [])
    {
        $hasAccess = true;
        foreach ($policies as $policy){
            $policyInstance = new $policy();
            
            if(method_exists($policyInstance, 'before')){
                $before = $policyInstance->before($user);
                if(!is_null($before)){
                    $hasAccess = $before;
                }
            }
            
            if($hasAccess){
                if($payload instanceof Model){
                    $hasAccess = $policyInstance->$action($user, $payload);
                }else{
                    $hasAccess = $policyInstance->$action($user);
                }
            }
            
            if(!$hasAccess) break;
        }
        
        return $hasAccess;
    }
}
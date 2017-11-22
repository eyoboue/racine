<?php


namespace Racine\Security\Authorization;


use Racine\Model;

trait UserAuthorizationTrait
{
    /**
     * @param string $action
     * @param null|string|Model $payload
     * @return bool
     */
    public function can($action, $payload = null)
    {
        return AuthorizationChecker::getInstance()->check($this, $action, $payload);
    }
}
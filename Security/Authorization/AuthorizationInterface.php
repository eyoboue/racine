<?php


namespace Racine\Security\Authorization;


use Racine\Model;

interface AuthorizationInterface
{
    /**
     * @param string $action
     * @param null|string|Model $payload
     * @return bool
     */
    public function can($action, $payload = null);
}
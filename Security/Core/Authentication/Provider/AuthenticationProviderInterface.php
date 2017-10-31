<?php

namespace Racine\Security\Core\Authentication\Provider;

use Racine\Security\Core\Authentication\AuthenticationManagerInterface;
use Racine\Security\Core\Authentication\Token\TokenInterface;

interface AuthenticationProviderInterface extends AuthenticationManagerInterface
{
    public function supports(TokenInterface $token);
}
<?php

namespace Racine\Security\Core\Exception;


class AuthenticationCredentialsNotFoundException extends AuthenticationException
{
    public function getMessageKey()
    {
        return 'Authentication credentials could not be found.';
    }
}
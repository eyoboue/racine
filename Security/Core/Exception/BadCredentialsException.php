<?php

namespace Racine\Security\Core\Exception;


class BadCredentialsException extends AuthenticationException
{
    public function getMessageKey()
    {
        return 'Invalid credentials.';
    }
}
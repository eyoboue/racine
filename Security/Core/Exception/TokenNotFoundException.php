<?php

namespace Racine\Security\Core\Exception;


class TokenNotFoundException extends AuthenticationException
{
    public function getMessageKey()
    {
        return 'No token could be found.';
    }
}
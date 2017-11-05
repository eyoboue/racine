<?php

namespace Security\Exception;


class BadCredentialsException extends AuthenticationException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Invalid credentials.';
    }
}
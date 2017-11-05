<?php

namespace Security\Exception;


class SessionUnavailableException extends AuthenticationException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'No session available, it either timed out or cookies are not enabled.';
    }
}
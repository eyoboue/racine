<?php

namespace Racine\Security;


final class Security
{
    const LOGGED_TOKEN = '_security.logged_token';
    const ACCESS_DENIED_ERROR = '_security.403_error';
    const AUTHENTICATION_ERROR = '_security.last_error';
    const LAST_USERNAME = '_security.last_username';
    const MAX_USERNAME_LENGTH = 4096;
}
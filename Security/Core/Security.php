<?php

namespace Racine\Security\Core;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
final class Security
{
    const ACCESS_DENIED_ERROR = '_security.403_error';
    const AUTHENTICATION_ERROR = '_security.last_error';
    const LAST_USERNAME = '_security.last_username';
    const MAX_USERNAME_LENGTH = 4096;
}
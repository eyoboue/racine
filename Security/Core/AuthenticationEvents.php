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
final class AuthenticationEvents
{
    /**
     * @var string
     */
    const AUTHENTICATION_SUCCESS = 'security.authentication.success';
    
    /**
     * @var string
     */
    const AUTHENTICATION_FAILURE = 'security.authentication.failure';
}
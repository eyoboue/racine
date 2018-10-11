<?php

namespace Racine\Security;


final class AuthenticationEvents
{
    /**
     * The AUTHENTICATION_SUCCESS event occurs after a user is authenticated by one provider.
     * The event listener method receives a Racine\Security\Event\AuthenticationEvent instance.
     *
     * @var  string
     */
    const AUTHENTICATION_SUCCESS = 'security.authentication.success';
    
    /**
     * The AUTHENTICATION_FAILURE event occurs after a user cannot be authenticated by any of the providers.
     * The event listener method receives a Racine\Security\Event\AuthenticationFailureEvent instance.
     *
     * @var string
     */
    const AUTHENTICATION_FAILURE = 'security.authentication.failure';
}
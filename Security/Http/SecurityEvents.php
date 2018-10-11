<?php

namespace Racine\Security\Http;


final class SecurityEvents
{
    /**
     * The INTERACTIVE_LOGIN event occurs after a user has actively logged into your website.
     * It is important to distinguish this action from non-interactive authentication methods,
     * such as: - authentication based on your session.
     *
     * Authentication using a HTTP basic or HTTP digest header.
     *
     * The event listener method receives a Racine\Security\Http\Event\InteractiveLoginEvent instance.
     *
     * @var string
     */
    const INTERACTIVE_LOGIN = 'security.interactive_login';
    
    /**
     * The INTERACTIVE_LOGIN_FAILURE event occurs after a user has failed to log in into your website
     * The event listener method receives a Racine\Security\Http\Event\InteractiveLoginFailureEvent instance.
     *
     * @var string
     */
    const INTERACTIVE_LOGIN_FAILURE = 'security.interactive_login_failure';
    
    /**
     * The INTERACTIVE_LOGOUT event occurs after a user has logged out from your website
     * The event listener method receives a Racine\Security\Http\Event\InteractiveLogoutEvent instance.
     *
     * @var string
     */
    const INTERACTIVE_LOGOUT = 'security.interactive_logout';
}
<?php

namespace Racine\Security\Http;


use Racine\Http\Request;

interface FirewallMapInterface
{
    /**
     * Returns the authentication listeners, and the exception listener to use
     * for the given request.
     *
     * If there are no authentication listeners, the first inner array must be
     * empty.
     *
     * If there is no exception listener, the second element of the outer array
     * must be null.
     *
     * @param Request $request
     *
     * @return array of the format array(array(AuthenticationListener), ExceptionListener)
     */
    public function getListeners(Request $request);
}
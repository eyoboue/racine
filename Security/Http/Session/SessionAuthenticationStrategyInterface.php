<?php

namespace Racine\Security\Http\Session;
use Racine\Http\Request;
use Racine\Security\Core\Authentication\Token\TokenInterface;

/**
 * SessionAuthenticationStrategyInterface.
 *
 * Implementation are responsible for updating the session after an interactive
 * authentication attempt was successful.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface SessionAuthenticationStrategyInterface
{
    /**
     * This performs any necessary changes to the session.
     *
     * This method is called before the TokenStorage is populated with a
     * Token, and only by classes inheriting from AbstractAuthenticationListener.
     *
     * @param Request        $request
     * @param TokenInterface $token
     */
    public function onAuthentication(Request $request, TokenInterface $token);
}
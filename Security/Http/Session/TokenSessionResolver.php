<?php

namespace Racine\Security\Http\Session;


use Racine\Http\Request;
use Racine\Http\Session;
use Racine\Security\Authentication\Token\TokenInterface;
use Racine\Security\Security;

class TokenSessionResolver
{
    /**
     * @var Session
     */
    private $session;
    
    public function __construct(Request $request)
    {
        $this->session = $request->getSession();
    }
    
    public function getToken()
    {
        $token = $this->session->get(Security::LOGGED_TOKEN);
        if(!is_null($token)){
            $token = unserialize($this->session->get(Security::LOGGED_TOKEN));
            if($token instanceof TokenInterface){
                return $token;
            }
        }
        return null;
    }
}
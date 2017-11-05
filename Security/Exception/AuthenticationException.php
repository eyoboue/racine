<?php

namespace Security\Exception;


use Racine\Security\Authentication\Token\TokenInterface;

class AuthenticationException extends \RuntimeException implements \Serializable
{
    private $token;
    
    /**
     * Get the token.
     *
     * @return TokenInterface
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * Set the token.
     *
     * @param TokenInterface $token
     */
    public function setToken(TokenInterface $token)
    {
        $this->token = $token;
    }
    
    public function serialize()
    {
        return serialize(array(
            $this->token,
            $this->code,
            $this->message,
            $this->file,
            $this->line,
        ));
    }
    
    public function unserialize($str)
    {
        list(
            $this->token,
            $this->code,
            $this->message,
            $this->file,
            $this->line
            ) = unserialize($str);
    }
    
    /**
     * Message key to be used by the translation component.
     *
     * @return string
     */
    public function getMessageKey()
    {
        return 'An authentication exception occurred.';
    }
    
    /**
     * Message data to be used by the translation component.
     *
     * @return array
     */
    public function getMessageData()
    {
        return array();
    }
}
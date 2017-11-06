<?php

namespace Racine\Security\Authentication\Provider;


use Racine\Security\Authentication\Token\TokenInterface;
use Racine\Security\Authentication\Token\UsernamePasswordToken;
use Racine\Security\User\UserInterface;
use Racine\Security\Exception\AuthenticationException;
use Racine\Security\Exception\AuthenticationServiceException;
use Racine\Security\Exception\BadCredentialsException;
use Racine\Security\Exception\UsernameNotFoundException;

abstract class UserAuthenticationProvider implements AuthenticationProviderInterface
{
    private $providerKey;
    
    /**
     * Constructor.
     *
     * @param string               $providerKey                A provider key
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($providerKey)
    {
        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }
        
        $this->providerKey = $providerKey;
    }
    
    /**
     * @return string
     */
    public function getProviderKey()
    {
        return $this->providerKey;
    }
    
    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }
        
        $username = $token->getUsername();
        if ('' === $username || null === $username) {
            return null;
        }
        
        try {
            $user = $this->retrieveUser($username, $token);
        } catch (UsernameNotFoundException $e) {
            $e->setUsername($username);
            
            throw $e;
        }
        
        if (!$user instanceof UserInterface) {
            throw new AuthenticationServiceException('retrieveUser() must return a UserInterface.');
        }
    
        try {
            $this->checkAuthentication($user, $token);
        } catch (BadCredentialsException $e) {
            throw $e;
        }
        
        $authenticatedToken = new UsernamePasswordToken($user, $token->getCredentials(), $this->getRoles($user, $token));
        $authenticatedToken->setAttributes($token->getAttributes());
        
        return $authenticatedToken;
    }
    
    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof UsernamePasswordToken;
    }
    
    /**
     * Retrieves roles from user and appends SwitchUserRole if original token contained one.
     *
     * @param UserInterface  $user  The user
     * @param TokenInterface $token The token
     *
     * @return array The user roles
     */
    private function getRoles(UserInterface $user, TokenInterface $token)
    {
        $roles = $user->getRoles();
        
        return $roles;
    }
    
    /**
     * Retrieves the user from an implementation-specific location.
     *
     * @param string                $username The username to retrieve
     * @param UsernamePasswordToken $token    The Token
     *
     * @return UserInterface The user
     *
     * @throws AuthenticationException if the credentials could not be validated
     */
    abstract protected function retrieveUser($username, UsernamePasswordToken $token);
    
    /**
     * Does additional checks on the user and token (like validating the
     * credentials).
     *
     * @param UserInterface         $user  The retrieved UserInterface instance
     * @param UsernamePasswordToken $token The UsernamePasswordToken token to be authenticated
     *
     * @throws AuthenticationException if the credentials could not be validated
     */
    abstract protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token);
}
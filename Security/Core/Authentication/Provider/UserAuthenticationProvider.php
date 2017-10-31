<?php

namespace Racine\Security\Core\Authentication\Provider;

use Racine\Security\Core\Authentication\Token\TokenInterface;
use Racine\Security\Core\Authentication\Token\UsernamePasswordToken;
use Racine\Security\Core\User\UserCheckerInterface;
use Racine\Security\Core\User\UserInterface;

abstract class UserAuthenticationProvider implements AuthenticationProviderInterface
{
    private $userChecker;
    
    public function __construct(UserCheckerInterface $userChecker)
    {
        $this->userChecker = $userChecker;
    }
    
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return;
        }
    
        // TODO
        return null;
        
        /*$username = $token->getUsername();
        if ('' === $username || null === $username) {
            $username = 'NONE_PROVIDED';
        }
        
        try {
            $user = $this->retrieveUser($username, $token);
        } catch (UsernameNotFoundException $e) {
            if ($this->hideUserNotFoundExceptions) {
                throw new BadCredentialsException('Bad credentials.', 0, $e);
            }
            $e->setUsername($username);
            
            throw $e;
        }
        
        if (!$user instanceof UserInterface) {
            throw new AuthenticationServiceException('retrieveUser() must return a UserInterface.');
        }
        
        try {
            $this->userChecker->checkPreAuth($user);
            $this->checkAuthentication($user, $token);
            $this->userChecker->checkPostAuth($user);
        } catch (BadCredentialsException $e) {
            if ($this->hideUserNotFoundExceptions) {
                throw new BadCredentialsException('Bad credentials.', 0, $e);
            }
            
            throw $e;
        }
        
        $authenticatedToken = new UsernamePasswordToken($user, $token->getCredentials(), $this->getRoles($user, $token));
        
        return $authenticatedToken;*/
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
        
        /*foreach ($token->getRoles() as $role) {
            if ($role instanceof SwitchUserRole) {
                $roles[] = $role;
                
                break;
            }
        }*/
        
        return $roles;
    }
    
    abstract protected function retrieveUser($username, UsernamePasswordToken $token);
    
    abstract protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token);
}
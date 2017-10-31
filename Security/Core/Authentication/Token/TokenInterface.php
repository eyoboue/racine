<?php

namespace Racine\Security\Core\Authentication\Token;

interface TokenInterface
{
    public function getCredentials();
    
    public function getUser();
    
    public function getRoles();
    
    public function setUser($user);
    
    public function getUsername();
    
    public function isAuthenticated();
    
    public function setAuthenticated($isAuthenticated);
    
    public function eraseCredentials();
}
<?php

namespace Racine\Security\Core\User;

interface UserInterface
{
    public function getRoles();
    
    public function getPassword();
    
    public function getUsername();
    
    public function eraseCredentials();
}
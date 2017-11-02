<?php


namespace Racine\Security\Core\User;

interface UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user);
    
    public function checkPostAuth(UserInterface $user);
}
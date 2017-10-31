<?php

namespace Racine\Security\Core\User;


interface AdvancedUserInterface extends UserInterface
{
    public function isEnabled();
}
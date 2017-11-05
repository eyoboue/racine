<?php

namespace Racine\Security\Role;


class Role implements RoleInterface
{
    /**
     * @var string
     */
    private $role;
    
    /**
     * Role constructor.
     * @param string $role
     */
    public function __construct($role)
    {
        $this->role = $role;
    }
    
    
    public function getRole()
    {
        return $this->role;
    }
    
}
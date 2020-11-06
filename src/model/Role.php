<?php

namespace tadmin\model;

class Role extends Model
{
    protected $name = 'roles';

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, RolePermission::class, "permission_id", "role_id");
    }
}

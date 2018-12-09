<?php

namespace tadmin\model;

class Role extends Model
{
    protected $table = 'roles';

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, RolePermission::class);
    }
}

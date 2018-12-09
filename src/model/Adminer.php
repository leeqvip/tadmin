<?php

namespace tadmin\model;

use tadmin\service\auth\contract\Authenticate;

class Adminer extends Model implements Authenticate
{
    protected $table = 'adminers';

    public function roles()
    {
        return $this->belongsToMany(Role::class, AdminerRole::class);
    }

    public function setAdminPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    public function retrieveByCredentials(array $credentials)
    {
        $query = $this->db();
        foreach ($credentials as $key => $value) {
            $query->where($key, $value);
        }

        return $query->find();
    }

    public function getAuthIdentifier()
    {
        return $this->{$this->pk};
    }

    public function retrieveByIdentifier($identifier)
    {
        return $this->find($identifier);
    }
}

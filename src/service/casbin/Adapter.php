<?php

namespace tadmin\service\casbin;

use Casbin\Persist\Adapter as AdapterContract;
use tadmin\model\Role;
use Casbin\Exceptions\CasbinException;
use Casbin\Persist\AdapterHelper;

class Adapter implements AdapterContract
{
    use AdapterHelper;

    protected $roleId;

    protected $role;

    public function __construct()
    {
        // $this->role = $role;
    }

    public function savePolicyLine($ptype, array $rule)
    {
    }

    public function loadPolicy($model)
    {
        $roles = Role::when(null !== $this->roleId, function ($query) {
            $query->where('id', $this->roleId);
        })
        ->with('permissions')
        ->select();

        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                $line = 'p, '.$permission->http_method.', '.$permission->http_path;
                $this->loadPolicyLine(trim($line), $model);
            }
        }
    }

    public function savePolicy($model)
    {
        foreach ($model->model['p'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }

        foreach ($model->model['g'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }

        return true;
    }

    public function addPolicy($sec, $ptype, $rule)
    {
        throw new CasbinException('not implemented');
    }

    public function removePolicy($sec, $ptype, $rule)
    {
        throw new CasbinException('not implemented');
    }

    public function removeFilteredPolicy($sec, $ptype, $fieldIndex, ...$fieldValues)
    {
        throw new CasbinException('not implemented');
    }
}

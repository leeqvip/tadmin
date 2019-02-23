<?php

namespace tadmin\service\casbin;

use Casbin\Persist\Adapter as AdapterContract;
use Casbin\Exceptions\CasbinException;
use Casbin\Persist\AdapterHelper;
use tadmin\model\AdminerRole;
use tadmin\model\Role;
use tadmin\service\auth\facade\Auth;

class Adapter implements AdapterContract
{
    use AdapterHelper;

    public function savePolicyLine($ptype, array $rule)
    {
    }

    public function loadPolicy($model)
    {
        $adminer = Auth::user();
        // 加载所有（当前登录用户）的角色及其权限
        $roles = Role::when($adminer, function ($query) use ($adminer) {
            $query->whereIn('id', $adminer->roles->column('id'));
        })
        ->with('permissions')
        ->select();

        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                $line = 'p, role.'.$role->id.', '.$permission->http_method.', '.$permission->http_path;
                $this->loadPolicyLine(trim($line), $model);
            }
        }

        // 加载（当前登录）用户和角色的关系
        $adminersOfRoles = AdminerRole::when($adminer, function ($query) use ($adminer) {
            $query->where('adminer_id', $adminer->id);
        })
        ->select();
        foreach ($adminersOfRoles as $aor) {
            $line = 'g, adminer.'.$aor->adminer_id.', role.'.$aor->role_id;
            $this->loadPolicyLine(trim($line), $model);
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

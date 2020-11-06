<?php

namespace tadmin\service\casbin;

use Casbin\Persist\Adapter as AdapterContract;
use Casbin\Exceptions\CasbinException;
use Casbin\Persist\AdapterHelper;
use tadmin\model\AdminerRole;
use tadmin\model\Role;
use tadmin\service\auth\facade\Auth;
use Casbin\Model\Model;

class Adapter implements AdapterContract
{
    use AdapterHelper;

    public function savePolicyLine($ptype, array $rule)
    {
    }

    /**
     * loads all policy rules from the storage.
     *
     * @param Model $model
     */
    public function loadPolicy(Model $model): void
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
                $line = 'p, role.' . $role->id . ', ' . $permission->http_method . ', ' . $permission->http_path;
                $this->loadPolicyLine(trim($line), $model);
            }
        }
        // 加载（当前登录）用户和角色的关系
        $adminersOfRoles = AdminerRole::when($adminer, function ($query) use ($adminer) {
            $query->where('adminer_id', $adminer->id);
        })
            ->select();
        foreach ($adminersOfRoles as $aor) {
            $line = 'g, adminer.' . $aor->adminer_id . ', role.' . $aor->role_id;
            $this->loadPolicyLine(trim($line), $model);
        }
    }

    /**
     * saves all policy rules to the storage.
     *
     * @param Model $model
     */
    public function savePolicy(Model $model): void
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
    }

    /**
     * adds a policy rule to the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     */
    public function addPolicy(string $sec, string $ptype, array $rule): void
    {
        throw new CasbinException('not implemented');
    }

    /**
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     */
    public function removePolicy(string $sec, string $ptype, array $rule): void
    {
        throw new CasbinException('not implemented');
    }

    /**
     * RemoveFilteredPolicy removes policy rules that match the filter from the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param int    $fieldIndex
     * @param string ...$fieldValues
     */
    public function removeFilteredPolicy(string $sec, string $ptype, int $fieldIndex, string ...$fieldValues): void

    {
        throw new CasbinException('not implemented');
    }
}

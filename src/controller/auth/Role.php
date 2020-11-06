<?php

namespace tadmin\controller\auth;

use tadmin\model\Permission;
use tadmin\model\Role as RoleModel;
use tadmin\support\controller\Controller;
use think\Request;

class Role extends Controller
{
    protected $role;

    public function __construct(RoleModel $role)
    {
        parent::__construct();
        $this->role = $role;
    }

    public function index()
    {
        $roles = $this->role->with('permissions')->paginate();

        return $this->fetch('auth/role/index', [
            'roles' => $roles,
        ]);
    }

    public function edit(Request $request, Permission $permission)
    {
        $role = $this->role->findOrEmpty($request->get('id', 0));
        $permissions = $permission->select();
        $permissionsIds = $role && !$role->isEmpty() ? $role->permissions()->column('id') : [];
        
        return $this->fetch('auth/role/edit', [
            'role' => $role,
            'permissions' => $permissions,
            'permissionsIds' => $permissionsIds,
        ]);
    }

    public function save(Request $request)
    {
        try {
            $data = $request->post();
            $role = $this->role->findOrEmpty($data['id'] ?? 0);
            $role->save($data);
            $permissionsIds = $role && $role->permissions ? \array_column($role->permissions->toArray(), 'id'): [];

            $newPermissionsIds = $request->post('permission_id', []);
            $newPermissionsIds = array_map(function ($item) {
                return (int) $item;
            }, $newPermissionsIds);
            if (!empty($permissionsIds)) {
                $detachPermissionsIds = array_diff(
                    array_merge($permissionsIds, $newPermissionsIds),
                    $newPermissionsIds
                );
                $attachPermissionsIds = array_diff(
                    $newPermissionsIds,
                    array_intersect($permissionsIds, $newPermissionsIds)
                );
            } else {
                $attachPermissionsIds = $newPermissionsIds;
            }

            if (isset($attachPermissionsIds) && !empty($attachPermissionsIds)) {
                $role->permissions()->attach(array_values($attachPermissionsIds));
            }

            if (isset($detachPermissionsIds) && !empty($detachPermissionsIds)) {
                $role->permissions()->detach(array_values($detachPermissionsIds));
            }
        } catch (\Exception $e) {
            throw $e;
            $this->error('保存失败');
        }
        return $this->redirect('tadmin.auth.role');
    }

    public function delete(Request $request)
    {
        try {
            $this->role->destroy($request->get('id'));
        } catch (\Exception $e) {
            return $this->error('删除失败');
        }

        return $this->success('删除成功');
    }
}

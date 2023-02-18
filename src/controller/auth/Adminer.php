<?php

namespace tadmin\controller\auth;

use tadmin\model\Adminer as AdminerModel;
use tadmin\model\Role;
use tadmin\support\controller\Controller;
use think\exception\ValidateException;
use think\facade\Validate;
use think\Request;

class Adminer extends Controller
{
    protected $adminer;

    public function __construct(AdminerModel $adminer)
    {
        parent::__construct();
        $this->adminer = $adminer;
    }

    public function index()
    {
        $adminers = $this->adminer->with('roles')->paginate();

        return $this->fetch('auth/adminer/index', [
            'adminers' => $adminers,
        ]);
    }

    public function edit(Request $request, Role $role)
    {
        $adminer = $this->adminer->with("roles")->find($request->get('id', 0));
        $roles = $role->select();
        $roleIds = $adminer && $adminer->roles ? \array_column($adminer->roles->toArray(), 'id'): [];
        return $this->fetch('auth/adminer/edit', compact('adminer', 'roleIds', 'roles'));
    }

    public function save(Request $request)
    {
        try {
            // $uploader->upload('avatar');

            if ($request->get('id') > 0) {
                $adminer = $this->updateAdminer($request);
            } else {
                $adminer = $this->createAdminer($request);
            }

            $this->updateRoles($adminer, $request->post('role_id', []));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return $this->redirect('tadmin.auth.adminer');
    }

    protected function updateRoles($adminer, $newRoleIds)
    {
        $roleIds = $adminer && $adminer->roles ? \array_column($adminer->roles->toArray(), 'id'): [];

        $newRoleIds = array_map(function ($item) {
            return (int) $item;
        }, $newRoleIds);
        if (!empty($roleIds)) {
            $detachRoleIds = array_diff(
                array_merge($roleIds, $newRoleIds),
                $newRoleIds
            );
            $attachRoleIds = array_diff(
                $newRoleIds,
                array_intersect($roleIds, $newRoleIds)
            );
        } else {
            $attachRoleIds = $newRoleIds;
        }

        if (isset($attachRoleIds) && !empty($attachRoleIds)) {
            $adminer->roles()->attach(array_values($attachRoleIds));
        }

        if (isset($detachRoleIds) && !empty($detachRoleIds)) {
            $adminer->roles()->detach(array_values($detachRoleIds));
        }
    }

    protected function createAdminer(Request $request)
    {
        $data = $request->only(['admin_account', 'admin_password', 'admin_password_confirm']);

        $this->validateAdminAccount($data);
        $this->validateAdminPassword($data);

        $adminer = $this->adminer->create($data);

        if (!$adminer) {
            throw new \Exception('创建管理员失败');
        }

        return $adminer;
    }

    protected function updateAdminer(Request $request)
    {
        $data = $request->only(['admin_password', 'admin_password_confirm', 'id']);
        if (isset($data['admin_password']) && !empty($data['admin_password'])) {
            $this->validateAdminPassword($data);
        } else {
            unset($data['admin_password']);
        }
        $adminer = $this->adminer->find($data['id']);
        $adminer->save($data);
        if (!$adminer) {
            throw new \Exception('修改管理员失败');
        }

        return $adminer;
    }

    protected function validateAdminAccount(array $data)
    {
        $validate = Validate::rule('admin_account', 'require|alphaDash|max:16|unique:adminers')
        ->message([
            'admin_account.require' => '登录账号必须',
            'admin_account.alphaDash' => '登录账号只能是字母、数字和下划线_及破折号-',
            'admin_account.max' => '登录账号最多不能超过16个字符',
            'admin_account.unique' => '登录账号被使用',
        ]);

        if (!$validate->check($data)) {
            throw new ValidateException($validate->getError());
        }
    }

    protected function validateAdminPassword(array $data)
    {
        $validate = Validate::rule('admin_password', 'require|alphaDash|confirm|max:16')
        ->message([
            'admin_password.require' => '登录密码必须',
            'admin_password.alphaDash' => '登录密码只能是字母、数字和下划线_及破折号-',
            'admin_password.max' => '登录密码最多不能超过16个字符',
            'admin_password.confirm' => '登录密码和确认密码不一致',
        ]);

        if (!$validate->check($data)) {
            throw new ValidateException($validate->getError());
        }
    }

    public function delete(Request $request)
    {
        try {
            if (1 == $request->get('id')) {
                throw new \Exception('该账号不能删除');
            }
            $this->adminer->destroy($request->get('id'));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        return $this->success('删除成功');
    }
}

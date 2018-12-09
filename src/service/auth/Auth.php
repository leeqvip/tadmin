<?php

namespace tadmin\service\auth;

use tadmin\service\auth\contract\Authenticate;
use tadmin\service\auth\guard\contract\Guard;
use think\exception\ValidateException;
use think\facade\Validate;
use think\Request;

class Auth implements contract\Auth
{
    public $failException = true;

    protected $authenticate;

    protected $adminer;

    protected $guard;

    public function __construct(Authenticate $authenticate, Guard $guard)
    {
        $this->authenticate = $authenticate;
        $this->guard = $guard;
    }

    public function login(Request $request)
    {
        $this->validate($request->param());

        if (!$this->attempt($request->param())) {
            throw new ValidateException('用户名或密码错误');
        }

        $this->guard()->login($this->adminer);
    }

    public function logout()
    {
        $this->guard()->logout();
    }

    public function user()
    {
        return $this->guard()->user();
    }

    public function guard()
    {
        return $this->guard;
    }

    protected function validate(array $data = [])
    {
        $validate = Validate::make([
            'admin_account' => 'require|max:25',
            'admin_password' => 'require|max:25',
        ], [
            'admin_account.require' => '登录名必须',
            'admin_account.max' => '登录名最多不能超过25个字符',
            'admin_password.require' => '密码必须',
            'admin_password.max' => '密码最多不能超过25个字符',
        ]);

        if (!$validate->check($data)) {
            if ($this->failException) {
                throw new ValidateException($validate->getError());
            }
        }
    }

    public function attempt(array $credentials)
    {
        $adminer = $this->adminer = $this->authenticate->retrieveByCredentials([
            'admin_account' => $credentials['admin_account'],
        ]);
        if (!$adminer) {
            return false;
        }

        return $this->validCredentials($adminer, $credentials);
    }

    protected function validCredentials($adminer, array $credentials)
    {
        return password_verify($credentials['admin_password'], $adminer->admin_password);
    }
}

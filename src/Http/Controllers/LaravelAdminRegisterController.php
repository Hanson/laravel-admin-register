<?php

namespace Hanson\LaravelAdminRegister\Http\Controllers;

use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Controllers\AuthController;
use Encore\Admin\Layout\Content;
use Hanson\LaravelAdminRegister\Administrator;
use Hanson\LaravelAdminRegister\RegisterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LaravelAdminRegisterController extends AuthController
{
    public function index(Content $content)
    {
        return $content
            ->title('Title')
            ->description('Description')
            ->body(view('laravel-admin-register::index'));
    }

    public function getRegister()
    {
        if ($this->guard()->check()) {
            return redirect($this->redirectPath());
        }

        return view('laravel-admin-register::register');
    }

    public function sendCode(Request $request, RegisterRepository $repository)
    {
        $request->validate([
            'mobile' => 'required|string|max:11'
        ]);

        return $repository->sendCode(request('mobile'));
    }

    public function postRegister(Request $request, RegisterRepository $repository)
    {
        $field = config('admin.extensions.laravel_admin_register.database.username_field', 'mobile');

        $data = Validator::make($request->all(), [
            $field => ['required', 'string', 'max:11'],
            'code' => ['required', 'string', 'max:4'],
            'password' => ['required', 'string', 'min:8'],
        ])->validate();

        if ($result = $repository->validate($data['mobile'], $data['code']) !== true) {
            return back()->withErrors($result);
        }

        if (DB::table($table = config('admin.database.users_table'))
            ->where($field, $data['mobile'])
            ->exists()) {
            return ['error_code' => 1, 'error_msg' => '该账号已注册，请直接登录'];
        }

        $admin = Administrator::query()->firstOrCreate([$field => $data['mobile']], ['password' => bcrypt($data['password'])]);

        $admin->roles()->attach(Role::query()->where('slug', config('admin.extensions.laravel_admin_register.register_as', 'administrator')));

        return redirect()->route('admin.login');
    }
}
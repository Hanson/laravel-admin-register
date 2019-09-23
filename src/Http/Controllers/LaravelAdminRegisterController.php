<?php

namespace Hanson\LaravelAdminRegister\Http\Controllers;

use App\Models\User;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AuthController;
use Encore\Admin\Layout\Content;
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
        $data = Validator::make($request->all(), [
            'mobile' => ['required', 'string', 'max:11'],
            'code' => ['required', 'string', 'max:4'],
            'password' => ['required', 'string', 'min:8'],
        ])->validate();

        if ($result = $repository->validate($data['mobile'], $data['code']) !== true) {
            return $result;
        }

        if (DB::table($table = config('admin.extensions.laravel_admin_register.database.table', 'users'))
            ->where($field = config('admin.extensions.laravel_admin_register.database.username_field', 'mobile'), $data['mobile'])
            ->exists()) {
            return ['error_code' => 1, 'error_msg' => '该账号已注册，请直接登录'];
        }

        DB::table($table)->insert([$field => $data['mobile'], 'password' => bcrypt($data['password'])]);

        return redirect(route('admin.login'));
    }
}
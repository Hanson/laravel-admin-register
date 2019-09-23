laravel-admin extension
======


https://github.com/overtrue/easy-sms

# 安装

安装依赖
```
composer require hanson/laravel-admin-register:dev-master
```

# 配置

发布资源，之后会生成一个 config/sms.php 配置，具体短信配置可查看 https://github.com/overtrue/easy-sms 
```
php artisan vendor:publish --provider=Hanson\LaravelAdminRegister\LaravelAdminRegisterServiceProvider
```

在 config/admin.php 添加配置

``` 
'extensions' => [
    'laravel_admin_register' => [
        'cache_key' => 'register.code.', // 缓存前缀
        'send_limit' => 6, // 限制 60 秒内只能发一次
        'expires_in' => 300, // 5 分钟（300秒）内有效
        'is_mock' => true, // 为 true 时不发短信，验证码为 0000
        'database' => [
            'username_field' => 'mobile',
        ]
    ]
],
```

跑 `php artisan admin:install`的时候，如果有改动用户表的字段，会标错，你可以执行下面的命令重新执行兼容性的版本
``` 
php artisan db:seed --class=\Hanson\LaravelAdminRegister\Database
```

在 app/Admin/Controllers/AuthController.php 中添加
``` 
class AuthController extends BaseAuthController
{
    public function postLogin(Request $request)
    {
        $this->loginValidator($request->all())->validate();

        $credentials = [
            config('admin.extensions.laravel_admin_register.database.username_field', 'mobile') => $request->get($this->username()),
            'password' => $request->get('password'),
        ];
        $remember = $request->get('remember', false);

        if ($this->guard()->attempt($credentials, $remember)) {
            return $this->sendLoginResponse($request);
        }

        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }
}
```

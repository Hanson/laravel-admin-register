laravel-admin extension
======

laravel-admin 短信注册扩展

# 安装

安装依赖
```
composer require hanson/laravel-admin-register:dev-master
```

# 配置

## 发布资源
```
php artisan vendor:publish --provider=Hanson\LaravelAdminRegister\LaravelAdminRegisterServiceProvider
```
发布资源，之后会生成一个 config/sms.php 配置，具体短信配置可查看 https://github.com/overtrue/easy-sms 

## 修改迁移
一般来说我们在执行完 `php artisan admin:install` 的时候，都会修改 `CreateAdminTables`,我这里改为 mobile 为主要唯一索引，所以下面的配置 `username_field` 我也改为 mobile
```php
Schema::create(config('admin.database.users_table'), function (Blueprint $table) {
    $table->increments('id');
    $table->string('mobile', 11)->unique();
    $table->string('password', 60);
    $table->string('remember_token', 100)->nullable();
    $table->timestamps();
});
```

## 执行填充
跑 `php artisan admin:install`的时候，如果有改动用户表的字段，会报错，你可以执行下面的命令重新执行兼容性的版本
``` 
php artisan db:seed --class=\Hanson\LaravelAdminRegister\AdminTablesSeeder
```

## 编辑登录
在 app/Admin/Controllers/AuthController.php 中添加
```php
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

## 编辑扩展配置
在 config/admin.php 添加配置

```php
'extensions' => [
    'laravel_admin_register' => [
        'cache_key' => 'register.code.', // 缓存前缀
        'send_limit' => 60, // 限制 60 秒内只能发一次
        'expires_in' => 300, // 5 分钟（300秒）内有效
        'is_mock' => true, // 为 true 时不发短信，验证码为 0000
        'database' => [
            'username_field' => 'mobile', // 管理员数据库唯一索引字段，也就是存储手机号码的字段
        ],
        'register_as' => 'administrator', // 用户注册的默认角色 slug，可以登录后去创建角色
    ]
],
```

## 短信发送自定义
编辑 app/Providers/AppServiceProvider.php
```php
public function register()
{
    // 返回内容参考 https://github.com/overtrue/easy-sms 中不同短信服务商的要求，此处返回 $easysms->send() 的第二个参数
    LaravelAdminRegister::setting(function ($code) {
        return ['content' => "验证码: $code ，请于5分钟内完成验证，若非本人操作，请忽略本短信。"];
    });
}
```

## 体验

执行 `php artisan serve`，然后你可以访问 http://127.0.0.1:8000/register 进行注册，测试环境验证码为 `0000` （见配置 is_mock）

你可以使用账号 `18000000000` 密码 `admin` 去登录创建角色，修改注册用户的默认角色（见配置 register_as）

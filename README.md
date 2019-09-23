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

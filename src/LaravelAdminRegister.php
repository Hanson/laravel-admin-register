<?php

namespace Hanson\LaravelAdminRegister;

use Encore\Admin\Extension;

class LaravelAdminRegister extends Extension
{
    public $name = 'laravel-admin-register';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    private static $callback;

    public static function setting($callback)
    {
        static::$callback = $callback;
    }

    public static function getSetting($code)
    {
        $callback = static::$callback;
        logger($callback($code));
        return $callback($code);
    }
}
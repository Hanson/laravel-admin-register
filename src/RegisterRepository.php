<?php

namespace Hanson\LaravelAdminRegister;

use Carbon\Carbon;
use Overtrue\EasySms\EasySms;
use Illuminate\Support\Facades\Cache;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class RegisterRepository
{
    public function sendCode(string $mobile)
    {
        $cacheKey = config('admin.extensions.laravel_admin_register.cache_key', 'register.code.').$mobile;

        if (($cache = Cache::get($cacheKey)) && Carbon::now()->diffInSeconds($cache['time']) < config('admin.extensions.laravel_admin_register.send_limit', 60)) {
            return ['error_code' => 1, 'err_msg' => '发送验证码频繁，请稍后尝试'];
        }

        $isMock = config('admin.extensions.laravel_admin_register.is_mock', false);

        Cache::put($cacheKey, [
            'code' => $code = $isMock ? '0000' : rand(1000, 9999),
            'time' => Carbon::now(),
        ], config('admin.extensions.laravel_admin_register.expires_in', 300));

        $easySme = new EasySms(config('sms'));

        if ($isMock) {
            return ['error_code' => 0];
        }

        try {
            $easySme->send($mobile, LaravelAdminRegister::getSetting($code));

            return ['error_code' => 0];
        } catch (NoGatewayAvailableException $exception) {
            logger($exception->getLastException());
            logger(json_decode($exception->getLastException()->getMessage(), true));
            return ['error_code' => 1, 'err_msg' => json_decode($exception->getLastException()->getMessage(), true)['errorMsg']];
        }
    }

    public function validate(string $mobile, string $code)
    {
        $cacheKey = config('admin.extensions.laravel_admin_register.cache_key', 'register.code.').$mobile;

        $cache = Cache::get($cacheKey);

        if (!$cache) {
            return '验证码已过期或不存在';
        }

        if ($cache['code'] != $code) {
            return '验证码不正确';
        }

        return true;
    }
}

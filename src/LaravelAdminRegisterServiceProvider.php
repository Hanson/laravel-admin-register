<?php

namespace Hanson\LaravelAdminRegister;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LaravelAdminRegisterServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(LaravelAdminRegister $extension)
    {
        if (! LaravelAdminRegister::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'laravel-admin-register');
        }

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__.'./../config' => config_path(),
                ],
                'laravel-admin-register'
            );
        }

        $this->app->booted(function () {
            Route::group(['middleware' => 'web'], __DIR__.'/../routes/web.php');
        });
    }
}
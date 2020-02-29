<?php

namespace Hanson\LaravelAdminRegister;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
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
                    __DIR__.'/./../config' => config_path(),
                ],
                'laravel-admin-register'
            );
            $this->registerSeedsFrom(__DIR__.'/./../database/seeds');
        }

        $this->app->booted(function () {
            Route::group(['middleware' => 'web'], __DIR__.'/../routes/web.php');
        });
    }

    protected function registerSeedsFrom($path)
    {
        foreach (glob("$path/*.php") as $filename)
        {
            include $filename;
            $classes = get_declared_classes();
            $class = end($classes);

            $command = Request::server('argv', null);
            if (is_array($command)) {
                $command = implode(' ', $command);
                if ($command == "artisan db:seed") {
                    Artisan::call('db:seed', ['--class' => $class]);
                }
            }

        }
    }
}

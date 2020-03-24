<?php

namespace InetStudio\Vkontakte\Users\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * Class UsersServiceProvider.
 */
class UsersServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     */
    public function boot(): void
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
    }

    /**
     * Регистрация команд.
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                'InetStudio\Vkontakte\Users\Console\Commands\CreateFoldersCommand',
                'InetStudio\Vkontakte\Users\Console\Commands\SetupCommand',
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../config/vkontakte_users.php' => config_path('vkontakte_users.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/filesystems.php', 'filesystems.disks'
        );

        if ($this->app->runningInConsole()) {
            if (! Schema::hasTable('vkontakte_users')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_vkontakte_users_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_vkontakte_users_tables.php'),
                ], 'migrations');
            }
        }
    }
}

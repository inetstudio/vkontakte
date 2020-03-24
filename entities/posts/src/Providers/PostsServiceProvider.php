<?php

namespace InetStudio\Vkontakte\Posts\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * Class PostsServiceProvider.
 */
class PostsServiceProvider extends ServiceProvider
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
                'InetStudio\Vkontakte\Posts\Console\Commands\CreateFoldersCommand',
                'InetStudio\Vkontakte\Posts\Console\Commands\SetupCommand',
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../config/vkontakte_posts.php' => config_path('vkontakte_posts.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/filesystems.php', 'filesystems.disks'
        );

        if ($this->app->runningInConsole()) {
            if (! Schema::hasTable('vkontakte_posts')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_vkontakte_posts_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_vkontakte_posts_tables.php'),
                ], 'migrations');
            }
        }
    }
}

<?php

namespace InetStudio\Vkontakte\Providers;

use Illuminate\Support\ServiceProvider;
use InetStudio\Vkontakte\Console\Commands\SetupCommand;
use InetStudio\Vkontakte\Console\Commands\CreateFoldersCommand;
use InetStudio\Vkontakte\Services\Back\VkontaktePostsService;
use InetStudio\Vkontakte\Services\Back\VkontakteUsersService;
use InetStudio\Vkontakte\Contracts\Services\Back\VkontaktePostsServiceContract;
use InetStudio\Vkontakte\Contracts\Services\Back\VkontakteUsersServiceContract;

/**
 * Class VkontakteServiceProvider
 * @package InetStudio\Vkontakte\Providers
 */
class VkontakteServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
    }

    /**
     * Регистрация привязки в контейнере.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    /**
     * Регистрация команд.
     *
     * @return void
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupCommand::class,
                CreateFoldersCommand::class,
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     *
     * @return void
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../config/vkontakte.php' => config_path('vkontakte.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/filesystems.php', 'filesystems.disks'
        );

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateVkontakteTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_vkontakte_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_vkontakte_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    protected function registerBindings(): void
    {
        $this->app->bind(VkontaktePostsServiceContract::class, VkontaktePostsService::class);
        $this->app->bind(VkontakteUsersServiceContract::class, VkontakteUsersService::class);
    }
}

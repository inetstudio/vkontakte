<?php

namespace InetStudio\Vkontakte\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class VkontakteServiceProvider.
 */
class VkontakteServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     */
    public function boot()
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
                'InetStudio\Vkontakte\Console\Commands\TestCommand',
                'InetStudio\Vkontakte\Console\Commands\SetupCommand',
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     */
    protected function registerPublishes(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/services.php', 'services'
        );
    }
}

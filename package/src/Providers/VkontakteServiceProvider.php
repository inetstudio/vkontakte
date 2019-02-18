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
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
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
                'InetStudio\Vkontakte\Console\Commands\TestCommand',
                'InetStudio\Vkontakte\Console\Commands\SetupCommand',
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
        $this->mergeConfigFrom(
            __DIR__.'/../../config/services.php', 'services'
        );
    }
}

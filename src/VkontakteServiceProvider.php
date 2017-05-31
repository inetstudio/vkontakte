<?php

namespace InetStudio\Vkontakte;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class VkontakteServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../database/' => base_path('database'),
        ], 'database');

        $this->publishes([
            __DIR__.'/../config/vkontakte.php' => config_path('vkontakte.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../config/filesystems.php', 'filesystems.disks'
        );
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('VkontakteUser', function () {
            return new VkontakteUser();
        });

        $this->app->singleton('VkontaktePost', function () {
            return new VkontaktePost();
        });

        $loader = AliasLoader::getInstance();
        $loader->alias('VkontaktePost', 'InetStudio\Vkontakte\Facades\VkontaktePostFacade');
        $loader->alias('VkontakteUser', 'InetStudio\Vkontakte\Facades\VkontakteUserFacade');

        $this->app->register('Spatie\MediaLibrary\MediaLibraryServiceProvider');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'VkontaktePost',
            'VkontakteUser',
        ];
    }
}

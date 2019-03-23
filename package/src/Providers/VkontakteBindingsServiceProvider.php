<?php

namespace InetStudio\Vkontakte\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class VkontakteBindingsServiceProvider.
 */
class VkontakteBindingsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\Vkontakte\Contracts\Services\Back\VkontakteServiceContract' => 'InetStudio\Vkontakte\Services\Back\VkontakteService',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return  array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}

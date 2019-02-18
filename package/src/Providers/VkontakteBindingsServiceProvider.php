<?php

namespace InetStudio\Vkontakte\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class VkontakteBindingsServiceProvider.
 */
class VkontakteBindingsServiceProvider extends ServiceProvider
{
    /**
    * @var  bool
    */
    protected $defer = true;

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

<?php

namespace InetStudio\Vkontakte\Posts\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class PostsBindingsServiceProvider.
 */
class PostsBindingsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\Vkontakte\Posts\Contracts\Repositories\PostsRepositoryContract' => 'InetStudio\Vkontakte\Posts\Repositories\PostsRepository',
        'InetStudio\Vkontakte\Posts\Contracts\Models\PostModelContract' => 'InetStudio\Vkontakte\Posts\Models\PostModel',
        'InetStudio\Vkontakte\Posts\Contracts\Services\Back\PostsServiceContract' => 'InetStudio\Vkontakte\Posts\Services\Back\PostsService',
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

<?php

namespace InetStudio\Vkontakte\Users\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class UsersBindingsServiceProvider.
 */
class UsersBindingsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\Vkontakte\Users\Contracts\Repositories\UsersRepositoryContract' => 'InetStudio\Vkontakte\Users\Repositories\UsersRepository',
        'InetStudio\Vkontakte\Users\Contracts\Models\UserModelContract' => 'InetStudio\Vkontakte\Users\Models\UserModel',
        'InetStudio\Vkontakte\Users\Contracts\Services\Back\UsersServiceContract' => 'InetStudio\Vkontakte\Users\Services\Back\UsersService',
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

<?php

namespace InetStudio\Vkontakte\Comments\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class CommentsBindingsServiceProvider.
 */
class CommentsBindingsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
    * @var array
    */
    public $bindings = [
        'InetStudio\Vkontakte\Comments\Contracts\Repositories\CommentsRepositoryContract' => 'InetStudio\Vkontakte\Comments\Repositories\CommentsRepository',
        'InetStudio\Vkontakte\Comments\Contracts\Models\CommentModelContract' => 'InetStudio\Vkontakte\Comments\Models\CommentModel',
        'InetStudio\Vkontakte\Comments\Contracts\Services\Back\CommentsServiceContract' => 'InetStudio\Vkontakte\Comments\Services\Back\CommentsService',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}

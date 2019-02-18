<?php

namespace InetStudio\Vkontakte\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class TestCommand.
 */
class TestCommand extends Command
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:vkontakte:test';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Test vkontakte';

    /**
     * Запуск команды.
     *
     * @return void
     */
    public function handle(): void
    {
        $vkontakteService = app()->make('InetStudio\Vkontakte\Contracts\Services\Back\VkontakteServiceContract');
        $vkontakteComments = app()->make('InetStudio\Vkontakte\Comments\Contracts\Services\Back\CommentsServiceContract');
        $vkontaktePosts = app()->make('InetStudio\Vkontakte\Posts\Contracts\Services\Back\PostsServiceContract');
        $vkontakteUsers = app()->make('InetStudio\Vkontakte\Users\Contracts\Services\Back\UsersServiceContract');

        /*
        $result = $vkontakteService->request('newsfeed', 'search', [
            'q' => '#collagenspecialist',
        ]);*/

        $result = $vkontakteService->request('wall', 'getById', ['posts' => '4548503_2735']);

        dd($result);
    }
}

<?php

namespace InetStudio\Vkontakte\Comments\Services\Back;

use InetStudio\AdminPanel\Services\Back\BaseService;
use InetStudio\Vkontakte\Comments\Contracts\Models\CommentModelContract;
use InetStudio\Vkontakte\Comments\Contracts\Services\Back\CommentsServiceContract;

/**
 * Class CommentsService.
 */
class CommentsService extends BaseService implements CommentsServiceContract
{
    /**
     * CommentsService constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->make('InetStudio\Vkontakte\Comments\Contracts\Repositories\CommentsRepositoryContract'));
    }

    /**
     * Сохраняем модель.
     *
     * @param array $comment
     *
     * @return CommentModelContract
     */
    public function save(array $comment): CommentModelContract
    {
        $data = [
            'comment_id' => $comment['owner_id'].'_'.$comment['id'],
            'post_id' => $comment['post_id'],
            'user_id' => $comment['from_id'],
            'additional_info' => $comment,
        ];

        $item = $this->repository->saveVkontakteObject($data, $comment['owner_id'].'_'.$comment['id']);

        return $item;
    }
}

<?php

namespace InetStudio\Vkontakte\Comments\Repositories;

use InetStudio\AdminPanel\Repositories\BaseRepository;
use InetStudio\Vkontakte\Comments\Contracts\Models\CommentModelContract;
use InetStudio\Vkontakte\Comments\Contracts\Repositories\CommentsRepositoryContract;

/**
 * Class CommentsRepository.
 */
class CommentsRepository extends BaseRepository implements CommentsRepositoryContract
{
    /**
     * CommentsRepository constructor.
     *
     * @param CommentModelContract $model
     */
    public function __construct(CommentModelContract $model)
    {
        $this->model = $model;

        $this->defaultColumns = ['id', 'comment_id', 'post_id', 'user_id', 'additional_info'];
        $this->relations = [
            'user' => function ($query) {
                $query->select(['id', 'user_id', 'additional_info']);
            },
            'post' => function ($query) {
                $query->select(['id', 'post_id', 'user_id', 'additional_info']);
            },
        ];
    }

    /**
     * Возвращаем объект по comment_id, либо создаем новый.
     *
     * @param string $commentId
     *
     * @return mixed
     */
    public function getItemByCommentId(string $commentId)
    {
        return $this->model::where('comment_id', '=', $commentId)->first() ?? new $this->model;
    }

    /**
     * Сохраняем объект.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function saveVkontakteObject(array $data)
    {
        $item = $this->getItemByCommentId($data['comment_id']);
        $item->fill($data);
        $item->save();

        return $item;
    }
}

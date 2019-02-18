<?php

namespace InetStudio\Vkontakte\Posts\Repositories;

use InetStudio\AdminPanel\Repositories\BaseRepository;
use InetStudio\Vkontakte\Posts\Contracts\Models\PostModelContract;
use InetStudio\Vkontakte\Posts\Contracts\Repositories\PostsRepositoryContract;

/**
 * Class PostsRepository.
 */
class PostsRepository extends BaseRepository implements PostsRepositoryContract
{
    /**
     * PostsRepository constructor.
     *
     * @param PostModelContract $model
     */
    public function __construct(PostModelContract $model)
    {
        $this->model = $model;

        $this->defaultColumns = ['id', 'post_id', 'user_id', 'additional_info'];
        $this->relations = [
            'user' => function ($query) {
                $query->select(['id', 'user_id', 'additional_info']);
            },
            'comments' => function ($query) {
                $query->select(['id', 'comment_id', 'post_id', 'user_id', 'additional_info']);
            },
            'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk', 'mime_type', 'custom_properties', 'responsive_images']);
            },
        ];
    }

    /**
     * Возвращаем объект по post_id, либо создаем новый.
     *
     * @param string $postId
     *
     * @return mixed
     */
    public function getItemByPostId(string $postId)
    {
        return $this->model::where('post_id', '=', $postId)->first() ?? new $this->model;
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
        $item = $this->getItemByPostId($data['post_id']);
        $item->fill($data);
        $item->save();

        return $item;
    }
}

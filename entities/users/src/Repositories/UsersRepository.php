<?php

namespace InetStudio\Vkontakte\Users\Repositories;

use InetStudio\AdminPanel\Repositories\BaseRepository;
use InetStudio\Vkontakte\Users\Contracts\Models\UserModelContract;
use InetStudio\Vkontakte\Users\Contracts\Repositories\UsersRepositoryContract;

/**
 * Class UsersRepository.
 */
class UsersRepository extends BaseRepository implements UsersRepositoryContract
{
    /**
     * UsersRepository constructor.
     *
     * @param UserModelContract $model
     */
    public function __construct(UserModelContract $model)
    {
        $this->model = $model;

        $this->defaultColumns = ['id', 'user_id', 'additional_info'];
        $this->relations = [
            'comments' => function ($query) {
                $query->select(['id', 'comment_id', 'post_id', 'user_id', 'additional_info']);
            },
            'posts' => function ($query) {
                $query->select(['id', 'post_id', 'user_id', 'additional_info']);
            },
            'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk', 'conversions_disk', 'uuid', 'mime_type', 'custom_properties', 'responsive_images']);
            },
        ];
    }

    /**
     * Возвращаем объект по user_id, либо создаем новый.
     *
     * @param string $userId
     *
     * @return mixed
     */
    public function getItemByUserId(string $userId)
    {
        return $this->model::where('user_id', '=', $userId)->first() ?? new $this->model;
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
        $item = $this->getItemByUserId($data['user_id']);
        $item->fill($data);
        $item->save();

        return $item;
    }
}

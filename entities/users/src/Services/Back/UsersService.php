<?php

namespace InetStudio\Vkontakte\Users\Services\Back;

use Illuminate\Support\Str;
use InetStudio\AdminPanel\Services\Back\BaseService;
use InetStudio\Vkontakte\Users\Contracts\Models\UserModelContract;
use InetStudio\Vkontakte\Users\Contracts\Services\Back\UsersServiceContract;

/**
 * Class UsersService.
 */
class UsersService extends BaseService implements UsersServiceContract
{
    /**
     * UsersService constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->make('InetStudio\Vkontakte\Users\Contracts\Repositories\UsersRepositoryContract'));
    }

    /**
     * Сохраняем модель.
     *
     * @param array $user
     *
     * @return UserModelContract|null
     */
    public function save(array $user): ?UserModelContract
    {
        if (empty($user)) {
            return null;
        }

        $data = [
            'user_id' => $user['id'],
            'additional_info' => $user,
        ];

        $item = $this->repository->saveVkontakteObject($data, $user['id']);
        $this->attachMedia($item, $user);

        return $item;
    }

    /**
     * Аттачим медиа к модели.
     *
     * @param UserModelContract $item
     * @param array $user
     */
    protected function attachMedia(UserModelContract $item, array $user): void
    {
        $name = md5(isset($user['photo_max_orig']) ? $user['photo_max_orig'] : 'empty');
        $currentMedia = $item->getMedia('media')->pluck('name')->toArray();

        if (isset($user['photo_max_orig']) && ! in_array($name, $currentMedia)) {
            $item->addMediaFromUrl($user['photo_max_orig'])
                ->usingName($name)
                ->toMediaCollection('media', 'vkontakte_users');
        }
    }

    /**
     * Добавляем информацию о пользователях к постам.
     *
     * @param array $posts
     *
     * @return array
     */
    public function attachUsersToPosts(array $posts): array
    {
        $vkontakteService = app()->make('InetStudio\Vkontakte\Contracts\Services\Back\VkontakteServiceContract');

        foreach ($posts as &$post) {
            if (! Str::startsWith($post['from_id'], '-')) {
                $result = $result = $vkontakteService->request('users', 'get', [
                    'user_ids' => $post['from_id'],
                ]);
                sleep(1);

                $post['user'] = $result[0] ?? [];
            }
        }

        return $posts;
    }
}

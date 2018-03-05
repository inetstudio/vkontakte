<?php

namespace InetStudio\Vkontakte\Services\Back;

use GuzzleHttp\Client;
use Emojione\Emojione as Emoji;
use InetStudio\Vkontakte\Models\VkontakteUserModel;
use InetStudio\Vkontakte\Contracts\Services\Back\VkontakteUsersServiceContract;

/**
 * Class VkontakteUsersService
 * @package InetStudio\Vkontakte\Services\Back
 */
class VkontakteUsersService implements VkontakteUsersServiceContract
{
    /**
     * Создание пользователя по его идентификатору.
     *
     * @param string $id
     * @return null
     */
    public function createUser($id = '')
    {
        if (! $id) {
            return;
        }

        $result = $this->sendRequest('users.get', [
            'user_ids' => $id,
            'fields' => 'nickname, screen_name, photo_id, has_photo, photo_50, photo_100, photo_200_orig, photo_200, photo_400_orig, photo_max, photo_max_orig, followers_count, common_count',
        ]);

        if (isset($result['response'][0])) {
            $user = $result['response'][0];
        } else {
            return;
        }

        $vkontakteUser = VkontakteUserModel::updateOrCreate([
            'user_id' => $user['id'],
        ], [
            'first_name' => (isset($user['first_name'])) ? Emoji::toShort($user['first_name']) : '',
            'last_name' => (isset($user['last_name'])) ? Emoji::toShort($user['last_name']) : '',
            'nickname' => (isset($user['nickname'])) ? Emoji::toShort($user['nickname']) : '',
            'screen_name' => (isset($user['screen_name'])) ? Emoji::toShort($user['screen_name']) : '',
            'has_photo' => $user['has_photo'],
            'photo_id' => (isset($user['photo_id'])) ? $user['photo_id'] : '',
            'photo_50' => (isset($user['photo_50'])) ? $user['photo_50'] : '',
            'photo_100' => (isset($user['photo_100'])) ? $user['photo_100'] : '',
            'photo_200' => (isset($user['photo_200'])) ? $user['photo_200'] : '',
            'photo_200_orig' => (isset($user['photo_200_orig'])) ? $user['photo_200_orig'] : '',
            'photo_400_orig' => (isset($user['photo_400_orig'])) ? $user['photo_400_orig'] : '',
            'photo_max' => (isset($user['photo_max'])) ? $user['photo_max'] : '',
            'photo_max_orig' => (isset($user['photo_max_orig'])) ? $user['photo_max_orig'] : '',
            'followers_count' => isset($user['following_count']) ? $user['following_count'] : 0,
            'common_count' => isset($user['media_count']) ? $user['media_count'] : 0,
        ]);

        return $vkontakteUser;
    }

    /**
     * Запрос к сервису для получения данных.
     *
     * @param $action
     * @param $params
     * @return mixed
     */
    private function sendRequest($action, $params)
    {
        $client = new Client();
        $response = $client->post(config('vkontakte.services.url').$action, [
            'form_params' => $params,
        ]);

        $media = json_decode($response->getBody()->getContents(), true);

        return $media;
    }
}

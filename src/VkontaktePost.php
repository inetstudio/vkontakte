<?php

namespace InetStudio\Vkontakte;

use GuzzleHttp\Client;
use Emojione\Emojione as Emoji;
use InetStudio\Vkontakte\Models\VkontaktePostModel;

class VkontaktePost
{
    /**
     * Создание поста по его идентификатору.
     *
     * @param string $id
     * @return null
     */
    public function createPost($id = '')
    {
        if (! $id) {
            return;
        }

        $result = $this->sendRequest('wall.getById', ['posts' => $id]);

        sleep(1);

        if (isset($result['response'][0])) {
            $post = $result['response'][0];
        } else {
            return;
        }

        $vkontaktePost = VkontaktePostModel::updateOrCreate([
            'post_id' => $post['from_id'].'_'.$post['id'],
        ], [
            'from_id' => (isset($post['from_id'])) ? $post['from_id'] : '',
            'owner_id' => (isset($post['owner_id'])) ? $post['owner_id'] : '',
            'post_source' => (isset($post['post_source']['type'])) ? $post['post_source']['type'] : '',
            'post_type' => (isset($post['post_type'])) ? $post['post_type'] : '',
            'text' => (isset($post['text'])) ? Emoji::toShort($post['text']) : '',
            'comments' => (isset($post['comments']['count'])) ? $post['comments']['count'] : 0,
            'likes' => (isset($post['likes']['count'])) ? $post['likes']['count'] : 0,
            'reposts' => (isset($post['reposts']['count'])) ? $post['reposts']['count'] : 0,
            'views' => (isset($post['views']['count'])) ? $post['views']['count'] : 0,
            'date' => $post['date'],
        ]);

        return $vkontaktePost;
    }

    /**
     * Поиск постов по тегу и их фильтрация по времени, типу, id.
     *
     * @param $tag
     * @param string $periodStart
     * @param string $periodEnd
     * @param array $filter
     * @param array $types
     * @return array
     */
    public function getPostsByTag($tag, $periodStart = '', $periodEnd = '', $filter = [], $types = [1, 2])
    {
        $haveData = true;
        $offset = 0;

        $postsArr = [];

        $startTime = ($periodStart) ? strtotime($periodStart) : null;
        $endTime = ($periodEnd) ? strtotime($periodEnd) : null;

        while ($haveData) {
            $result = $this->sendRequest('newsfeed.search', ['q' => $tag, 'count' => 200, 'offset' => $offset]);

            sleep(1);

            if (isset($result['response'])) {
                $all = $this->getFilteredPosts($result['response'], $startTime, $endTime, $filter, $types);

                $postsArr = array_merge($postsArr, $all['posts']);
            }

            if (count($result['response']) > 1) {
                $offset += 200;
            } else {
                $haveData = false;
            }
        }

        return array_reverse($postsArr);
    }

    /**
     * Фильтрация постов.
     *
     * @param $posts
     * @param $startTime
     * @param $endTime
     * @param $filter
     * @param $types
     * @return mixed
     */
    private function getFilteredPosts($posts, $startTime, $endTime, $filter, $types)
    {
        $filteredPosts['posts'] = [];
        $filteredPosts['stop'] = false;

        foreach ($posts as $post) {
            if (isset($post['id'])) {
                if (in_array($post['from_id'].'_'.$post['id'], $filter) or ! $this->checkAttacmentsTypes($post, $types)) {
                    continue;
                }

                if ($endTime and $post['date'] > $endTime) {
                    continue;
                }

                if ($startTime and $post['date'] < $startTime) {
                    $filteredPosts['stop'] = true;
                    break;
                } else {
                    array_push($filteredPosts['posts'], $post);
                }
            }
        }

        return $filteredPosts;
    }

    /**
     * Проверяем тип вложений.
     *
     * @param $post
     * @param $types
     * @return bool|void
     */
    private function checkAttacmentsTypes($post, $types)
    {
        if (! isset($post['attachments'])) {
            return;
        }

        foreach ($post['attachments'] as $attachment) {
            if (in_array($attachment['type'], $types)) {
                return true;
            }
        }

        return false;
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
<?php

namespace InetStudio\Vkontakte\Services\Back;

use GuzzleHttp\Client;
use Emojione\Emojione as Emoji;
use InetStudio\Vkontakte\Models\VkontaktePostModel;
use InetStudio\Vkontakte\Models\Attachments\LinkModel;
use InetStudio\Vkontakte\Models\Attachments\PhotoModel;
use InetStudio\Vkontakte\Models\Attachments\VideoModel;
use InetStudio\Vkontakte\Contracts\Services\Back\VkontaktePostsServiceContract;

/**
 * Class VkontaktePostsService
 * @package InetStudio\Vkontakte\Services\Back
 */
class VkontaktePostsService implements VkontaktePostsServiceContract
{
    /**
     * Создание поста по его идентификатору.
     *
     * @param string $id
     *
     * @return VkontaktePostModel|null
     */
    public function createPost($id = ''): ? VkontaktePostModel
    {
        $post = $this->getPostByID($id);

        if (! $post) {
            return null;
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

        if (isset($post['attachments'])) {
            foreach ($post['attachments'] as $attachment) {
                switch ($attachment['type']) {
                    case 'link':
                        LinkModel::updateOrCreate([
                            'post_id' => $vkontaktePost->id,
                            'url' => $attachment['link']['url'],
                        ], [
                            'title' => (isset($attachment['link']['title'])) ? $attachment['link']['title'] : '',
                            'description' => (isset($attachment['link']['description'])) ? $attachment['link']['description'] : '',
                            'target' => (isset($attachment['link']['target'])) ? $attachment['link']['target'] : '',
                        ]);
                        break;
                    case 'photo':
                        PhotoModel::updateOrCreate([
                            'post_id' => $vkontaktePost->id,
                            'pid' => (isset($attachment['photo']['id'])) ? $attachment['photo']['id'] : '',
                        ], [
                            'aid' => (isset($attachment['photo']['album_id'])) ? $attachment['photo']['album_id'] : '',
                            'owner_id' => (isset($attachment['photo']['owner_id'])) ? $attachment['photo']['owner_id'] : '',
                            'src' => (isset($attachment['photo']['photo_75'])) ? $attachment['photo']['photo_75'] : '',
                            'src_big' => (isset($attachment['photo']['photo_130'])) ? $attachment['photo']['photo_130'] : '',
                            'src_small' => (isset($attachment['photo']['photo_604'])) ? $attachment['photo']['photo_604'] : '',
                            'src_xbig' => (isset($attachment['photo']['photo_807'])) ? $attachment['photo']['photo_807'] : '',
                            'src_xxbig' => (isset($attachment['photo']['photo_1280'])) ? $attachment['photo']['photo_1280'] : '',
                            'src_xxxbig' => (isset($attachment['photo']['photo_2560'])) ? $attachment['photo']['photo_2560'] : '',
                            'width' => (isset($attachment['photo']['width'])) ? $attachment['photo']['width'] : 0,
                            'height' => (isset($attachment['photo']['height'])) ? $attachment['photo']['height'] : 0,
                            'text' => (isset($attachment['photo']['text'])) ? Emoji::toShort($attachment['photo']['text']) : '',
                            'date' => $attachment['photo']['date'],
                        ]);
                        break;
                    case 'video':
                        VideoModel::updateOrCreate([
                            'post_id' => $vkontaktePost->id,
                            'vid' => (isset($attachment['video']['vid'])) ? $attachment['video']['vid'] : '',
                        ], [
                            'owner_id' => (isset($attachment['video']['owner_id'])) ? $attachment['video']['owner_id'] : '',
                            'title' => (isset($attachment['video']['title'])) ? Emoji::toShort($attachment['video']['title']) : '',
                            'duration' => (isset($attachment['video']['duration'])) ? $attachment['video']['duration'] : 0,
                            'description' => (isset($attachment['video']['description'])) ? Emoji::toShort($attachment['video']['description']) : '',
                            'views' => (isset($attachment['video']['views'])) ? $attachment['video']['views'] : 0,
                            'image' => (isset($attachment['video']['image'])) ? $attachment['video']['image'] : '',
                            'image_big' => (isset($attachment['video']['image_big'])) ? $attachment['video']['image_big'] : '',
                            'image_small' => (isset($attachment['video']['image_small'])) ? $attachment['video']['image_small'] : '',
                            'date' => $attachment['video']['date'],
                        ]);
                        break;
                }
            }
        }

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
        $startFrom = '';

        $postsArr = [];

        $startTime = ($periodStart) ? strtotime($periodStart) : null;
        $endTime = ($periodEnd) ? strtotime($periodEnd) : null;

        $searchTag = (is_array($tag)) ? array_values($tag)[0] : $tag;
        $searchTag = $this->prepareTag($searchTag);

        while ($haveData) {
            $result = $this->sendRequest('newsfeed.search', ['q' => $searchTag, 'count' => 200, 'start_from' => $startFrom]);
            sleep(5);

            if (isset($result['response'])) {
                $all = $this->getFilteredPosts($result['response']['items'], $tag, $startTime, $endTime, $filter, $types);

                $postsArr = array_merge($postsArr, $all['posts']);
            }

            if (isset($result['response']['next_from'])) {
                $startFrom = $result['response']['next_from'];
            } else {
                $haveData = false;
            }
        }

        return array_reverse($postsArr);
    }

    /**
     * Получаем пост из Vkontakte.
     *
     * @param string $id
     *
     * @return array|null
     */
    public function getPostByID(string $id = ''): ?array
    {
        if (! $id) {
            return null;
        }

        $result = $this->sendRequest('wall.getById', ['posts' => $id]);
        sleep(5);

        if (isset($result['response'][0])) {
            $post = $result['response'][0];
        } else {
            return null;
        }

        return $post;
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
    private function getFilteredPosts($posts, $tag, $startTime, $endTime, $filter, $types)
    {
        $filteredPosts = [];

        $filteredPosts['posts'] = [];
        $filteredPosts['stop'] = false;

        $tag = $this->prepareTag($tag);

        foreach ($posts as $post) {
            if (isset($post['id'])) {
                if (in_array($post['from_id'].'_'.$post['id'], $filter) || ! $this->checkAttacmentsTypes($post, $types)) {
                    continue;
                }

                if ($endTime && $post['date'] > $endTime) {
                    continue;
                }

                if (! $this->checkTextTags($post, $tag)) {
                    continue;
                }

                if ($startTime && $post['date'] < $startTime) {
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
     * Проверяем наличие тегов в посте.
     *
     * @param $post
     * @param $tag
     *
     * @return bool
     */
    private function checkTextTags($post, $tag): bool
    {
        $result = false;

        if (is_array($tag)) {
            $texts = [];
            $texts[] = (isset($post['text'])) ? Emoji::toShort($post['text']) : '';

            if (isset($post['attachments'])) {
                foreach ($post['attachments'] as $attachment) {
                    switch ($attachment['type']) {
                        case 'link':
                            $texts[] = $attachment['link']['title'];
                            $texts[] = $attachment['link']['description'];
                            break;
                        case 'photo':
                            $texts[] = $attachment['photo']['text'];
                            break;
                        case 'video':
                            $texts[] = $attachment['video']['title'];
                            $texts[] = $attachment['video']['description'];
                            break;
                    }
                }
            }

            foreach ($texts as $text) {
                preg_match_all('/(#[а-яА-Яa-zA-Z0-9]+)/u', $text, $postTags);
                $postTags = array_map(function ($value) {
                    return mb_strtolower($value);
                }, $postTags[0]);

                if (count(array_intersect($tag, $postTags)) == count($tag)) {
                    $result = true;

                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Проверяем тип вложений.
     *
     * @param $post
     * @param $types
     *
     * @return bool
     */
    private function checkAttacmentsTypes($post, $types): bool
    {
        if (! isset($post['attachments'])) {
            return false;
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
     *
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

    /**
     * Приводим полученные теги к нужному виду.
     *
     * @param $tag
     *
     * @return array|string
     */
    private function prepareTag($tag)
    {
        if (is_array($tag)) {
            return array_map(function ($value) {
                return '#'.trim(mb_strtolower($value), '#');
            }, $tag);
        } else {
            return '#'.trim(mb_strtolower($tag), '#');
        }
    }
}

<?php

namespace InetStudio\Vkontakte\Posts\Services\Back;

use Spatie\MediaLibrary\Models\Media;
use InetStudio\AdminPanel\Services\Back\BaseService;
use InetStudio\Vkontakte\Posts\Contracts\Models\PostModelContract;
use InetStudio\Vkontakte\Posts\Contracts\Services\Back\PostsServiceContract;

/**
 * Class PostsService.
 */
class PostsService extends BaseService implements PostsServiceContract
{
    /**
     * PostsService constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->make('InetStudio\Vkontakte\Posts\Contracts\Repositories\PostsRepositoryContract'));
    }

    /**
     * Сохраняем модель.
     *
     * @param array $post
     *
     * @return PostModelContract
     */
    public function save(array $post): PostModelContract
    {
        $data = [
            'post_id' => $post['owner_id'].'_'.$post['id'],
            'user_id' => $post['from_id'],
            'additional_info' => $post,
        ];

        $item = $this->repository->saveVkontakteObject($data, $post['owner_id'].'_'.$post['id']);
        $this->attachMedia($item, $post);

        return $item;
    }


    /**
     * Аттачим медиа к модели.
     *
     * @param PostModelContract $item
     * @param array $post
     */
    protected function attachMedia(PostModelContract $item, array $post): void
    {
        $currentMedia = $item->getMedia('media')->pluck('name')->toArray();

        foreach ($post['attachments'] ?? [] as $attachment) {
            switch ($attachment['type']) {
                case 'photo':
                    $this->attachImage($item, $attachment['photo'] ?? [],'media', $currentMedia);
                    break;
                case 'video':
                    $this->attachVideo($item, $attachment['video'] ?? [], 'media', $currentMedia);
                    break;
                case 'link':
                    $this->attachImage($item, $attachment['link']['photo'] ?? [],'media', $currentMedia);
                    break;
            }
        }
    }

    /**
     * Аттачим фото к модели.
     *
     * @param PostModelContract $item
     * @param array $image
     * @param string $collection
     * @param array $currentMedia
     *
     * @return Media
     */
    protected function attachImage(PostModelContract $item,
                                   array $image,
                                   string $collection = 'media',
                                   array $currentMedia = []): ?Media
    {
        $imageAttach = null;

        if (empty($image)) {
            return $imageAttach;
        }

        $imageCandidate = $this->getCandidate($image['sizes']);
        $name = md5($imageCandidate['url']);

        if (isset($imageCandidate['url']) && ! in_array($name, $currentMedia)) {
            $imageAttach = $item->addMediaFromUrl($imageCandidate['url'])
                ->usingName($name)
                ->withCustomProperties($image)
                ->toMediaCollection($collection, 'vkontakte_posts');
        }

        return $imageAttach;
    }

    /**
     * Аттачим видео к модели.
     *
     * @param PostModelContract $item
     * @param array $video
     * @param string $collection
     * @param array $currentMedia
     *
     * @return Media
     */
    protected function attachVideo(PostModelContract $item,
                                   array $video,
                                   string $collection = 'media',
                                   array $currentMedia = []): ?Media
    {
        $videoAttach = null;

        return $videoAttach;
    }

    /**
     * Возвращаем изображение с максимальными размерами.
     *
     * @param array $sizes
     *
     * @return array
     */
    protected function getCandidate(array $sizes): array
    {
        $width = 0;
        $indexOfMaxImage = 0;

        foreach ($sizes as $index => $size) {
            if ($size['width'] > $width) {
                $width = $size['width'];
                $indexOfMaxImage = $index;
            }
        }

        return $sizes[$indexOfMaxImage];
    }

    /**
     * Поиск постов по тегу и их фильтрация.
     *
     * @param mixed $tag
     * @param array $filters
     *
     * @return array
     */
    public function getPostsByTag($tag, array $filters = []): array
    {
        $vkontakteService = app()->make('InetStudio\Vkontakte\Contracts\Services\Back\VkontakteServiceContract');

        $haveData = true;
        $stop = false;

        $startFrom = '';
        $postsArr = [];

        $searchTag = (is_array($tag)) ? array_values($tag)[0] : $tag;

        while ($haveData && ! $stop) {
            $result = $vkontakteService->request('newsfeed', 'search', [
                'q' => '#'.$searchTag,
                'count' => 200,
                'start_from' => $startFrom
            ]);
            sleep(1);

            if ($items = $result['items']) {
                $all = $this->filterPosts($items, $filters);

                $postsArr = array_merge($postsArr, $all['posts']);
                $stop = $all['stop'];
            }

            $haveData = (!! ($result['next_from'] ?? false));
            $startFrom = $result['next_from'] ?? '';
        }

        return $postsArr;
    }

    /**
     * Фильтрация постов.
     *
     * @param array $posts
     * @param array $filters
     *
     * @return array
     */
    protected function filterPosts(array $posts, array $filters = []): array
    {
        $filteredPosts = [];
        $filteredPosts['posts'] = [];
        $filteredPosts['stop'] = false;

        $pipeLine = app('Illuminate\Pipeline\Pipeline');
        foreach ($posts as $post) {
            if (isset($filters['startTime']) && $filters['startTime']->startTime && (int) $post['date'] < $filters['startTime']->startTime) {
                $filteredPosts['stop'] = true;

                break;
            }

            $post = $pipeLine
                ->send($post)
                ->through($filters)
                ->then(function ($post) {
                    return $post;
                });

            if (! $post) {
                continue;
            }

            array_push($filteredPosts['posts'], $post);
        }

        return $filteredPosts;
    }

    /**
     * Получаем пост по id.
     *
     * @param string $id
     *
     * @return array|null
     */
    public function getPostById(string $id = ''): ?array
    {
        if (! $id) {
            return null;
        }

        $vkontakteService = app()->make('InetStudio\Vkontakte\Contracts\Services\Back\VkontakteServiceContract');

        $result = $vkontakteService->request('wall', 'getById', [
            'posts' => $id,
        ]);

        return $result ?? null;
    }
}

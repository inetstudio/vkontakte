<?php

namespace InetStudio\Vkontakte\Posts\Pipelines\Filters;

use Closure;
use Emojione\Emojione as Emoji;

/**
 * Class ByTags.
 */
class ByTags
{
    /**
     * @var array
     */
    protected $tag;

    /**
     * ByTags constructor.
     *
     * @param mixed $tag
     */
    public function __construct($tag)
    {
        $this->tag = $this->prepareTag($tag);
    }

    /**
     * @param mixed $post
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($post, Closure $next)
    {
        if ($post && is_array($this->tag) && count($this->tag) > 1) {
            $result = false;
            $texts = [];

            $texts[] = $this->prepareCaption($post['text'] ?? '');

            if (isset($post['attachments'])) {
                foreach ($post['attachments'] as $attachment) {
                    switch ($attachment['type']) {
                        case 'link':
                            $texts[] = $this->prepareCaption($attachment['link']['title'] ?? '');
                            $texts[] = $this->prepareCaption($attachment['link']['description'] ?? '');
                            break;
                        case 'photo':
                            $texts[] = $this->prepareCaption($attachment['photo']['text'] ?? '');
                            break;
                        case 'video':
                            $texts[] = $this->prepareCaption($attachment['video']['title'] ?? '');
                            $texts[] = $this->prepareCaption($attachment['video']['description'] ?? '');
                            break;
                    }
                }
            }

            foreach ($texts as $text) {
                preg_match_all('/(#[а-яА-Яa-zA-Z0-9]+)/u', $text, $postTags);
                $postTags = array_map(function ($value) {
                    return mb_strtolower($value);
                }, $postTags[0]);

                if (count(array_intersect($this->tag, $postTags)) == count($this->tag)) {
                    $result = true;

                    break;
                }
            }

            if (! $result) {
                $post = null;
            }
        }

        return $next($post);
    }

    /**
     * Приводим полученные теги к нужному виду.
     *
     * @param $tag
     *
     * @return array|string
     */
    protected function prepareTag($tag)
    {
        if (is_array($tag)) {
            return array_map(function ($value) {
                return '#'.trim($value, '#');
            }, $tag);
        } else {
            return '#'.trim($tag, '#');
        }
    }

    /**
     * Приводим текст к нужному виду.
     *
     * @param string $text
     *
     * @return string
     */
    protected function prepareCaption(string $text): string
    {
        $caption = ($text) ? Emoji::toShort($text) : '';
        $caption = preg_replace('/:pound_symbol:/', '#', $caption);

        return $caption;
    }
}

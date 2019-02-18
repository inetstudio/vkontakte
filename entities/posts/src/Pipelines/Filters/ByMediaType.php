<?php

namespace InetStudio\Vkontakte\Posts\Pipelines\Filters;

use Closure;

/**
 * Class ByMediaType.
 */
class ByMediaType
{
    /**
     * @var array
     */
    protected $mediaTypes;

    /**
     * ByMediaType constructor.
     *
     * @param array $mediaTypes
     */
    public function __construct(array $mediaTypes = [])
    {
       $this->mediaTypes = $mediaTypes;
    }

    /**
     * @param mixed $post
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($post, Closure $next)
    {
        if (! $post) {
            return null;
        }

        $hasNeedleType = false;

        foreach ($post['attachments'] ?? [] as $attachment) {
            if (in_array($attachment['type'], $this->mediaTypes)) {
                $hasNeedleType = true;
            }
        }

        if (! $hasNeedleType) {
            return null;
        }

        return $next($post);
    }
}

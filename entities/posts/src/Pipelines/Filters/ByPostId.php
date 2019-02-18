<?php

namespace InetStudio\Vkontakte\Posts\Pipelines\Filters;

use Closure;

/**
 * Class ByPostId.
 */
class ByPostId
{
    /**
     * @var array
     */
    protected $postsIDs;

    /**
     * ByPostId constructor.
     *
     * @param array $postsIDs
     */
    public function __construct(array $postsIDs = [])
    {
       $this->postsIDs = $postsIDs;
    }

    /**
     * @param mixed $post
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($post, Closure $next)
    {
        if (! ($post && ! in_array($post['owner_id'].'_'.$post['id'], $this->postsIDs))) {
            $post = null;
        }

        return $next($post);
    }
}

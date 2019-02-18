<?php

namespace InetStudio\Vkontakte\Posts\Pipelines\Filters;

use Closure;

/**
 * Class ByUserId.
 */
class ByUserId
{
    /**
     * @var array
     */
    protected $usersIDs;

    /**
     * ByUserId constructor.
     *
     * @param array $usersIDs
     */
    public function __construct(array $usersIDs = [])
    {
       $this->usersIDs = $usersIDs;
    }

    /**
     * @param mixed $post
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($post, Closure $next)
    {
        if (! ($post && ! in_array((string) $post['from_id'], $this->usersIDs))) {
            $post = null;
        }

        return $next($post);
    }
}

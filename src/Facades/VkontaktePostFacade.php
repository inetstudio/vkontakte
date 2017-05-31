<?php

namespace Inetstudio\Vkontakte\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class InstagramPostFacade.
 */
class VkontaktePostFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'VkontaktePost';
    }
}

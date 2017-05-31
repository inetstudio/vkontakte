<?php

namespace Inetstudio\Vkontakte\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class InstagramUserFacade.
 */
class VkontakteUserFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'VkontakteUser';
    }
}

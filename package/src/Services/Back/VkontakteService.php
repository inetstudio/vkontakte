<?php

namespace InetStudio\Vkontakte\Services\Back;

use VK\Client\VKApiClient;
use VK\Client\Enums\VKLanguage;
use InetStudio\Vkontakte\Contracts\Services\Back\VkontakteServiceContract;

/**
 * Class VkontakteService.
 */
class VkontakteService implements VkontakteServiceContract
{
    /**
     * @var VKApiClient
     */
    protected $vkontakte;

    /**
     * Сервисный ключ.
     *
     * @var string
     */
    protected $serviceKey;

    /**
     * VkontakteService constructor.
     */
    public function __construct()
    {
        $this->serviceKey = config('services.vkontakte_api.service_key');
        $apiVersion = config('services.vkontakte_api.version', '5.92');

        $this->vkontakte = new VKApiClient($apiVersion, VKLanguage::RUSSIAN);
    }

    /**
     * Запрос в инстаграм.
     *
     * @param string $collection
     * @param string $method
     * @param array $params
     *
     * @return array
     */
    public function request(string $collection, string $method, array $params = []): array
    {
        $params = [
            $this->serviceKey,
            $params,
        ];

        $result = call_user_func_array(array($this->vkontakte->$collection(), $method), $params);

        return $result;
    }
}

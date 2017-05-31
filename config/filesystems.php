<?php

return [

    /*
     * Расширение файла конфигурации app/config/filesystems.php
     * добавляет локальные диски для хранения изображений постов и пользователей
     */

    'vkontakte_posts' => [
        'driver' => 'local',
        'root' => storage_path('app/public/vkontakte/posts/'),
        'url' => env('APP_URL').'/storage/vkontakte/posts/',
        'visibility' => 'public',
    ],

    'vkontakte_users' => [
        'driver' => 'local',
        'root' => storage_path('app/public/vkontakte/users/'),
        'url' => env('APP_URL').'/storage/vkontakte/users/',
        'visibility' => 'public',
    ],

];

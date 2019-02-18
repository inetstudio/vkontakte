<?php

return [

    /*
     * Расширение файла конфигурации app/config/filesystems.php
     * добавляет локальный диск для хранения медиа постов
     */

    'vkontakte_posts' => [
        'driver' => 'local',
        'root' => storage_path('app/public/vkontakte/posts'),
        'url' => env('APP_URL').'/storage/vkontakte/posts',
        'visibility' => 'public',
    ],
];

<?php

return [

    /*
     * Расширение файла конфигурации app/config/filesystems.php
     * добавляет локальные диск для хранения медиа пользователей
     */

    'vkontakte_users' => [
        'driver' => 'local',
        'root' => storage_path('app/public/vkontakte/users'),
        'url' => env('APP_URL').'/storage/vkontakte/users',
        'visibility' => 'public',
    ],

];

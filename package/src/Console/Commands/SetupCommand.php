<?php

namespace InetStudio\Vkontakte\Console\Commands;

use InetStudio\AdminPanel\Base\Console\Commands\BaseSetupCommand;

/**
 * Class SetupCommand
 * @package InetStudio\Vkontakte\Console\Commands
 */
class SetupCommand extends BaseSetupCommand
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:vkontakte:setup';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Setup vkontakte package';

    /**
     * Инициализация команд.
     *
     * @return void
     */
    protected function initCommands(): void
    {
        $this->calls = [
            [
                'type' => 'artisan',
                'description' => 'Vkontakte comments setup',
                'command' => 'inetstudio:vkontakte:comments:setup',
            ],
            [
                'type' => 'artisan',
                'description' => 'Vkontakte posts setup',
                'command' => 'inetstudio:vkontakte:posts:setup',
            ],
            [
                'type' => 'artisan',
                'description' => 'Vkontakte users setup',
                'command' => 'inetstudio:vkontakte:users:setup',
            ],
        ];
    }
}

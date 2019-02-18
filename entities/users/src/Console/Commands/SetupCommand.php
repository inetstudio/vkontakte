<?php

namespace InetStudio\Vkontakte\Users\Console\Commands;

use InetStudio\AdminPanel\Console\Commands\BaseSetupCommand;

/**
 * Class SetupCommand.
 */
class SetupCommand extends BaseSetupCommand
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:vkontakte:users:setup';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Setup vkontakte users package';

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
                'description' => 'Create folders',
                'command' => 'inetstudio:vkontakte:users:folders',
            ],
            [
                'type' => 'artisan',
                'description' => 'Publish migrations',
                'command' => 'vendor:publish',
                'params' => [
                    '--provider' => 'InetStudio\Vkontakte\Users\Providers\UsersServiceProvider',
                    '--tag' => 'migrations',
                ],
            ],
            [
                'type' => 'artisan',
                'description' => 'Migration',
                'command' => 'migrate',
            ],
            [
                'type' => 'artisan',
                'description' => 'Publish config',
                'command' => 'vendor:publish',
                'params' => [
                    '--provider' => 'InetStudio\Vkontakte\Users\Providers\UsersServiceProvider',
                    '--tag' => 'config',
                ],
            ],
        ];
    }
}

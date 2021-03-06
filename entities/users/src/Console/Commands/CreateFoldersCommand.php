<?php

namespace InetStudio\Vkontakte\Users\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class CreateFoldersCommand.
 */
class CreateFoldersCommand extends Command
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:vkontakte:users:folders';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Create vkontakte users folders';

    /**
     * Запуск команды.
     */
    public function handle(): void
    {
        $folders = [
            'vkontakte_users',
        ];

        foreach ($folders as $folder) {
            if (config('filesystems.disks.'.$folder)) {
                $path = config('filesystems.disks.'.$folder.'.root');
                $this->createDir($path);
            }
        }
    }

    /**
     * Создание директории.
     *
     * @param $path
     */
    private function createDir($path): void
    {
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
            $this->info($path.' Has been created.');
        } else {
            $this->info($path.' Already created.');
        }
    }
}

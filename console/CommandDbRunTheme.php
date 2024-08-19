<?php
namespace SkillDo\DevTool\Console;

use JetBrains\PhpStorm\NoReturn;
use SkillDo\DevTool\Commands\Command;

class CommandDbRunTheme extends Command
{
    protected string $signature = 'db:run:theme {file=database}';

    protected string $description = 'run migrations file in theme';

    #[NoReturn]
    public function handle(): bool
    {
        $file = $this->argument('file');

        $file = ($file === null || $file === true) ? 'database' : $file;

        if(!preg_match('/^[a-zA-Z0-9._-]+$/', $file)) {
            $this->line('Error: Tên file không hợp lệ');
            $this->line($this->fullCommand());
            $this->line('file name: '.$file);
            return self::ERROR;
        }

        $folder = 'views/'.\Theme::name();

        $path = $folder.'/database/'.$file.'.php';

        if(!file_exists($path)) {
            $this->line('Error: file database '.$path.' is not found.');
            $this->line($this->fullCommand());
            $this->line($path);
            return self::ERROR;
        }

        try {

            $database = include_once $path;

            $database->up();

            $this->line('success', 'green');

            return self::SUCCESS;
        }
        catch(\Exception $e) {

            $this->line($e->getMessage());

            return self::ERROR;
        }
    }
}
<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;
use JetBrains\PhpStorm\NoReturn;

class CommandMakeDatabaseTheme extends Command
{
    protected string $signature = 'make:db:theme {file=database}';

    protected string $description = 'Generates the database theme';

    #[NoReturn]
    public function handle(): bool
    {
        $file = $this->argument('file');

        $file = ($file === null || $file === true) ? 'database' : $file;

        if(!preg_match('/^[a-zA-Z0-9.-]+$/', $file)) {
            $this->line('Error: Tên file không hợp lệ');
            $this->line($this->fullCommand());
            $this->line('file name: '.$file);
            return self::ERROR;
        }

        $folder = \Theme::name();

        $path = $folder.'/database/'.$file.'.php';

        $samplePath = 'plugins/DevTool/sample/database.php';

        if(file_exists('views/'.$path)) {
            $this->line('Error: file database views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
            return self::ERROR;
        }

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file database sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$samplePath);
            return self::ERROR;
        }

        $storage = \Storage::disk('views');

        if(!file_exists('views/'.$folder.'/database')) {
            mkdir('views/'.$folder.'/database', 0775);
        }

        $storage->copy($samplePath, $path);

        $this->line(function (Message $message) use ($file) {
            $message->line('success!', 'green');
            $message->line('file is created');
            $message->line($file, 'blue');
        });

        return self::SUCCESS;
    }
}
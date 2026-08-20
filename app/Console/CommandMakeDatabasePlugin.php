<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use Illuminate\Support\Facades\Storage;

class CommandMakeDatabasePlugin extends Command
{
    protected string $signature = 'make:db:plugin {plugin} {file=database}';

    protected string $description = 'Generates the database plugin';

    public function handle(): bool
    {
        $plugin = $this->argument('plugin');

        if(!preg_match('/^[a-zA-Z0-9-]+$/', $plugin))
        {
            $this->line('Error: tên thư mục plugin không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ plugin: '.$plugin);
            return self::ERROR;
        }

        $file = $this->argument('file');

        $file = ($file === null || $file === true) ? 'database' : $file;

        if(!preg_match('/^[a-zA-Z0-9.-]+$/', $file))
        {
            $this->line('Error: Tên file không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ file name: '.$file);
            return self::ERROR;
        }

        $storage = Storage::disk('plugins');

        $path = $plugin.'/database/'.$file.'.php';

        $samplePath = 'DevTool/sample/database.php';

        if($storage->has($path))
        {
            $this->line('Error: file database '.$path.' is exits.');
            $this->line($this->fullCommand());
            $this->line($path);
            return self::ERROR;
        }

        if(!$storage->has($samplePath))
        {
            $this->line('Error: file database sample not found.');
            $this->line($this->fullCommand());
            $this->line($samplePath);
            return self::ERROR;
        }

        $storage->copy($samplePath, $path);

        $this->line(function (Message $message) use ($file)
        {
            $message->line('success!', 'green');
            $message->line('file is created');
            $message->line($file, 'blue');
        });

        return self::SUCCESS;
    }
}

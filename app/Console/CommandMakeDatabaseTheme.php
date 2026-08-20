<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use Illuminate\Support\Facades\Storage;
use SkillDo\Support\Path;

class CommandMakeDatabaseTheme extends Command
{
    protected string $signature = 'make:db:theme {file=database}';

    protected string $description = 'Generates the database theme';

    
    public function handle(): bool
    {
        $file = $this->argument('file');

        $file = ($file === null || $file === true) ? 'database' : $file;

        if(!preg_match('/^[a-zA-Z0-9.-]+$/', $file))
        {
            $this->line('Error: Tên file không hợp lệ');
            $this->line($this->fullCommand());
            $this->line('file name: '.$file);
            return self::ERROR;
        }

        $storage = Storage::disk('views');

        $folder = config('cms.theme.child');

        $path = $folder.'/database/'.$file.'.php';

        $samplePath = Path::plugin('DevTool/sample/database.php');

        if($storage->has($path))
        {
            $this->line('Error: file database views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
            return self::ERROR;
        }

        if(!file_exists($samplePath))
        {
            $this->line('Error: file database sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        $storage->put($path, file_get_contents($samplePath));

        $this->line(function (Message $message) use ($file)
        {
            $message->line('success!', 'green');
            $message->line('file is created');
            $message->line($file, 'blue');
        });

        return self::SUCCESS;
    }
}
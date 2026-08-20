<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Support\Path;

class CommandMakeMacroTheme extends Command {

    protected string $signature = 'make:macro:theme {class}';

    protected string $description = 'Generates macro file';

    public function handle(): bool
    {
        $class = $this->argument('class');

        if(!preg_match('/^[a-zA-Z0-9_]+$/', (string) $class))
        {
            $this->line('Error: Tên class không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        $class = Str::studly($class);

        $storage = Storage::disk('views');

        /*
         * Macro không đi qua PSR-4, Loader::bootTheme preload toàn bộ app/Macros
         * của cả theme cha lẫn theme con
         */
        $path = config('cms.theme.child').'/app/Macros/'.$class.'.php';

        if($storage->has($path))
        {
            $this->line('Error: file macro views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
            return self::ERROR;
        }

        $samplePath = Path::plugin('DevTool/sample/core/macro.php');

        if(!file_exists($samplePath))
        {
            $this->line('Error: file macro sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        $fileSample = str_replace('MacroClassName', $class, file_get_contents($samplePath));

        if(!$storage->put($path, $fileSample))
        {
            $this->line('Error: Tạo file macro thất bại kiểm tra lại quyền đọc ghi thư mục');
            return self::ERROR;
        }

        $this->line(function (Message $message) use ($class) {
            $message->line('success!', 'green');
            $message->line('macro');
            $message->line($class, 'blue');
            $message->line('is created');
        });

        return self::SUCCESS;
    }
}

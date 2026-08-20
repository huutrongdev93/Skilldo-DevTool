<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Cms\Plugin\Plugin;
use SkillDo\Support\Path;

class CommandMakeMacroPlugin extends Command {

    protected string $signature = 'make:macro:plugin {plugin} {class}';

    protected string $description = 'Generates macro file';

    public function handle(): bool
    {
        $pluginName = $this->argument('plugin');

        if(!preg_match('/^[a-zA-Z0-9._-]+$/', (string) $pluginName))
        {
            $this->line('Error: tên thư mục plugin không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ plugin: '.$pluginName);
            return self::ERROR;
        }

        $plugin = Plugin::get($pluginName);

        if(empty($plugin))
        {
            $this->line('Error: plugin not found.');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        $class = $this->argument('class');

        if(!preg_match('/^[a-zA-Z0-9_]+$/', (string) $class))
        {
            $this->line('Error: Tên class không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        $class = Str::studly($class);

        $storage = Storage::disk('plugins');

        /*
         * Macro không đi qua PSR-4, Loader::bootPlugin preload toàn bộ app/Macros
         */
        $path = $plugin->getId().'/app/Macros/'.$class.'.php';

        if($storage->has($path))
        {
            $this->line('Error: file macro '.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$path);
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

<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Cms\Plugin\Plugin;
use SkillDo\Support\Path;

class CommandMakeTable extends Command {

    protected string $signature = 'make:table {table} {class} {--plugin=}';

    protected string $description = 'Generates a data table class';

    public function handle(): bool
    {
        $table = $this->argument('table');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', (string) $table))
        {
            $this->line('Error: Tên module table không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ table name: '.$table);
            return self::ERROR;
        }

        $class = $this->argument('class');

        if(!preg_match('/^[a-zA-Z0-9_]+$/', (string) $class))
        {
            $this->line('Error: Tên class table không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        $class = Str::studly($class);

        if(class_exists($class))
        {
            $this->line('Error: Class này đã được sử dụng');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        $pluginName = $this->option('plugin');

        if(!empty($pluginName) && is_string($pluginName))
        {
            $plugin = Plugin::get($pluginName);

            if(empty($plugin))
            {
                $this->line('Error: plugin not found.');
                $this->line('+ '.$this->fullCommand());
                return self::ERROR;
            }

            return $this->creatFile(
                Storage::disk('plugins'),
                $plugin->getId(),
                DevtoolHelper::pluginAlias($plugin).'\Modules\Admin',
                $table,
                $class
            );
        }

        return $this->creatFile(
            Storage::disk('views'),
            config('cms.theme.child'),
            'Theme\Modules\Admin',
            $table,
            $class
        );
    }

    public function creatFile($storage, string $base, string $namespace, string $table, string $class): bool
    {
        /*
         * Loader map sẵn <Alias|Theme>\Modules => app/Modules
         */
        $path = $base.'/app/Modules/Admin/'.$class.'.php';

        if($storage->has($path))
        {
            $this->line('Error: file table '.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$path);
            return self::ERROR;
        }

        $samplePath = Path::plugin('DevTool/sample/table.php');

        if(!file_exists($samplePath))
        {
            $this->line('Error: file table sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        $fileSample = Str::of(file_get_contents($samplePath))
            ->replace('TABLE_NS', $namespace)
            ->replace('TABLE_CLASS_NAME', $class)
            ->replace('TABLE_NAME', $table)
            ->toString();

        if(!$storage->put($path, $fileSample))
        {
            $this->line('Error: Tạo file table thất bại kiểm tra lại quyền đọc ghi thư mục');
            return self::ERROR;
        }

        $this->line(function (Message $message) use ($namespace, $class) {
            $message->line('success!', 'green');
            $message->line('table');
            $message->line($namespace.'\\'.$class, 'blue');
            $message->line('is created');
        });

        return self::SUCCESS;
    }
}

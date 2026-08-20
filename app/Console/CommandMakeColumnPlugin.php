<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Cms\Plugin\Plugin;
use SkillDo\Support\Path;

class CommandMakeColumnPlugin extends Command {

    protected string $signature = 'make:column:plugin {plugin} {class}';

    protected string $description = 'Generates column class for table';

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
         * Loader map sẵn <Alias>\Cms\ => app/Cms/ nên column phải nằm trong app/Cms
         */
        $path = $plugin->getId().'/app/Cms/Table/Columns/'.$class.'.php';

        if($storage->has($path))
        {
            $this->line('Error: file column '.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$path);
            return self::ERROR;
        }

        $samplePath = Path::plugin('DevTool/sample/core/column.php');

        if(!file_exists($samplePath))
        {
            $this->line('Error: file column sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        $namespace = DevtoolHelper::pluginAlias($plugin).'\Cms\Table\Columns';

        $fileSample = Str::of(file_get_contents($samplePath))
            ->replace('COLUMN_NAMESPACE', $namespace)
            ->replace('COLUMN_CLASS_NAME', $class)
            ->toString();

        if(!$storage->put($path, $fileSample))
        {
            $this->line('Error: Tạo file column thất bại kiểm tra lại quyền đọc ghi thư mục');
            return self::ERROR;
        }

        $this->line(function (Message $message) use ($namespace, $class) {
            $message->line('success!', 'green');
            $message->line('column');
            $message->line($namespace.'\\'.$class, 'blue');
            $message->line('is created');
        });

        return self::SUCCESS;
    }
}

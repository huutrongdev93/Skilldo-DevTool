<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Cms\Plugin\Plugin;
use SkillDo\Support\Path;

class CommandMakeMiddlewarePlugin extends Command {

    protected string $signature = 'make:middleware:plugin {plugin} {class}';

    protected string $description = 'Generates a middleware (register alias in plugin.json)';

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

        if(!preg_match('/^[a-zA-Z0-9_-]+$/', (string) $class))
        {
            $this->line('Error: Tên class Middleware không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        $class = Str::studly($class);

        $alias = Str::camel($class);

        $storage = Storage::disk('plugins');

        /*
         * Loader map sẵn <Alias>\Middlewares => app/Middlewares
         */
        $path = $plugin->getId().'/app/Middlewares/'.$class.'.php';

        if($storage->has($path))
        {
            $this->line('Error: file middleware '.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$path);
            return self::ERROR;
        }

        $samplePath = Path::plugin('DevTool/sample/middleware.php');

        if(!file_exists($samplePath))
        {
            $this->line('Error: file middleware sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        $namespace = DevtoolHelper::pluginAlias($plugin).'\Middlewares';

        $fileSample = Str::of(file_get_contents($samplePath))
            ->replace('MIDDLEWARE_NAMESPACE', $namespace)
            ->replace('MIDDLEWARE_CLASS_NAME', $class)
            ->replace('MIDDLEWARE_ALIAS', $alias)
            ->toString();

        if(!$storage->put($path, $fileSample))
        {
            $this->line('Error: Tạo file middleware thất bại kiểm tra lại quyền đọc ghi thư mục');
            return self::ERROR;
        }

        $registered = DevtoolHelper::registerManifestMiddlewareAlias(
            Path::plugin($plugin->getId().'/plugin.json'),
            $alias,
            $namespace.'\\'.$class
        );

        if(!$registered)
        {
            $this->line('Error: không ghi được vào '.$plugin->getId().'/plugin.json');
            $this->line('+ '.$this->fullCommand());
            $storage->delete($path);
            return self::ERROR;
        }

        $this->line(function (Message $message) use ($class, $alias) {
            $message->line('success!', 'green');
            $message->line('middleware');
            $message->line($class, 'blue');
            $message->line('is created, alias: '.$alias);
        });

        return self::SUCCESS;
    }
}

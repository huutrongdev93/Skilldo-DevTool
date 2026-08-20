<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Support\Path;

class CommandMakeMiddlewareTheme extends Command {

    protected string $signature = 'make:middleware:theme {class}';

    protected string $description = 'Generates a middleware (register alias in theme-child.json)';

    public function handle(): bool
    {
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

        $storage = Storage::disk('views');

        /*
         * Loader map sẵn Theme\Middlewares => app/Middlewares của theme và theme-child
         */
        $path = config('cms.theme.child').'/app/Middlewares/'.$class.'.php';

        if($storage->has($path))
        {
            $this->line('Error: file middleware views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
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

        $namespace = 'Theme\Middlewares';

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
            Path::themeChild('theme-child.json'),
            $alias,
            $namespace.'\\'.$class,
            createIfMissing: true
        );

        if(!$registered)
        {
            $this->line('Error: không ghi được vào '.config('cms.theme.child').'/theme-child.json');
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

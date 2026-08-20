<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Support\Path;

class CommandMakeColumnTheme extends Command {

    protected string $signature = 'make:column:theme {class}';

    protected string $description = 'Generates column class for table';

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
         * Loader map sẵn Theme\Cms\ => app/Cms/ của theme và theme-child
         */
        $path = config('cms.theme.child').'/app/Cms/Table/Columns/'.$class.'.php';

        if($storage->has($path))
        {
            $this->line('Error: file column views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
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

        $namespace = 'Theme\Cms\Table\Columns';

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

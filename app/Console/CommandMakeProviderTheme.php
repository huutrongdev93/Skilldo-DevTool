<?php
namespace DevTool\Console;
use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Support\Path;

class CommandMakeProviderTheme extends Command {

    protected string $signature = 'make:provider:theme {name}';

    protected string $description = 'Generates an provider';

    public function handle(): bool
    {
        $name = $this->argument('name');

        if(!preg_match('/^[A-Za-z0-9\\\\\-_]+$/', $name))
        {
            $this->line('Error: Tên class provider không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ provider name: '.$name);
            return self::ERROR;
        }

        $folder = config('cms.theme.child');

        return $this->creatFile($folder, $name);
    }

    public function creatFile($folder, $name): bool
    {
        $storage = Storage::disk('views');

        [
            'namespace' => $namespace,
            'filename'  => $filename
        ] = DevtoolHelper::getNamespaceAndClass($name);

        $folder = $folder . DIRECTORY_SEPARATOR . 'app' .DIRECTORY_SEPARATOR.'Providers'.DIRECTORY_SEPARATOR;

        $path = $folder.((!empty($namespace)) ? str_replace('\\', '/', $namespace).'/': '').$filename.'ServiceProvider.php';

        if($storage->has($path))
        {
            $this->line('Error: file provider '.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$path);
            return self::ERROR;
        }

        $samplePath = Path::plugin('DevTool/sample/provider.php');

        if(!file_exists($samplePath))
        {
            $this->line('Error: file provider sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        if(!empty($namespace) && !$storage->exists($folder.DIRECTORY_SEPARATOR.$namespace))
        {
            $storage->makeDirectory(path_normalize($folder.DIRECTORY_SEPARATOR.$namespace));
        }

        $namespace = rtrim('Theme\Providers\\'.$namespace, '\\');

        $fileSample = file_get_contents($samplePath);

        $fileSample = Str::of($fileSample)
            ->replace('_NAMESPACE', $namespace)
            ->replace('_CLASS_NAME', $filename)
            ->toString();

        if($storage->put($path, $fileSample))
        {
            $this->line(function (Message $message) use ($filename) {
                $message->line('success!', 'green');
                $message->line('Provider '.$filename.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file provider thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }
}
<?php
namespace DevTool\Console;
use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use SkillDo\Cms\Plugin\Plugin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Support\Path;

class CommandMakeControllerPlugin extends Command {

    protected string $signature = 'make:controller:plugin {plugin} {name}';

    protected string $description = 'Generates an controller theme';

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

        $plugin = Plugin::get($plugin);

        if(empty($plugin))
        {
            $this->line('Error: plugin not found.');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        $name = $this->argument('name');

        if(!preg_match('/^[A-Za-z0-9\\\\\-\/_]+$/', $name))
        {
            $this->line('Error: Tên file controller không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ controller name: '.$name);
            return self::ERROR;
        }

        return $this->creatFile($plugin, $name);
    }

    public function creatFile($plugin, $name): bool
    {
        $folder = $plugin->getId() . DIRECTORY_SEPARATOR . 'app' .DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR;

        $storage = Storage::disk('plugins');

        [
            'namespace' => $namespace,
            'filename'  => $filename
        ] = DevtoolHelper::getNamespaceAndClass($name);

        $path = $folder.((!empty($namespace)) ? str_replace('\\', '/', $namespace).'/': '').$filename.'Controller.php';

        if($storage->has($path))
        {
            $this->line('Error: file controller '.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$path);
            return self::ERROR;
        }

        $samplePath = Path::plugin('DevTool/sample/controller.php');

        if(!file_exists($samplePath))
        {
            $this->line('Error: file controller sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        if(!empty($namespace) && !$storage->exists($folder.DIRECTORY_SEPARATOR.$namespace))
        {
            $storage->makeDirectory(path_normalize($folder.DIRECTORY_SEPARATOR.$namespace));
        }

        $manifest = $plugin->getManifest();

        $namespace = rtrim(($manifest['autoload']['alias'] ?? Str::studly($plugin->getId())).'\Controllers\\'.$namespace, '\\');

        $fileSample = file_get_contents($samplePath);

        $fileSample = Str::of($fileSample)
            ->replace('NAMESPACE_CLASS_NAME', $namespace)
            ->replace('CONTROLLER_CLASS_NAME', $filename)
            ->toString();

        if($storage->put($path, $fileSample))
        {
            $this->line(function (Message $message) use ($filename) {
                $message->line('success!', 'green');
                $message->line('controller '.$filename.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file controller thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }
}
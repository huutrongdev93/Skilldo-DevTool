<?php
namespace DevTool\Console;
use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use Illuminate\Support\Str;
use SkillDo\Cms\Plugin\Plugin;
use SkillDo\Support\Path;

class CommandMakeServicePlugin extends Command {

    protected string $signature = 'make:services:plugin {plugin} {name}';

    protected string $description = 'Generates an services plugin';

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

        if(!preg_match('/^[A-Za-z0-9\\\\\-_]+$/', $name))
        {
            $this->line('Error: Tên class services không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ services name: '.$name);
            return self::ERROR;
        }

        return $this->creatFile($plugin, $name);
    }

    public function creatFile($plugin, $file): bool
    {
        $folder = $plugin->getId() . DIRECTORY_SEPARATOR . 'app' .DIRECTORY_SEPARATOR.'Services'.DIRECTORY_SEPARATOR;

        [
            'namespace' => $namespace,
            'filename'  => $filename
        ] = DevtoolHelper::getNamespaceAndClass($file);

        $path = $folder.((!empty($namespace)) ? str_replace('\\', '/', $namespace).'/': '').$filename.'Service.php';

        if(file_exists(Path::plugin($path)))
        {
            $this->line('Error: file services plugin '.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$path);
            return self::ERROR;
        }

        $samplePath = Path::plugin('DevTool/sample/class.php');

        if(!file_exists($samplePath))
        {
            $this->line('Error: file services sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        $storage = \Storage::disk('plugins');

        if(!empty($namespace) && !$storage->exists($folder.DIRECTORY_SEPARATOR.$namespace))
        {
            $storage->makeDirectory(path_normalize($folder.DIRECTORY_SEPARATOR.$namespace));
        }

        $namespace = rtrim(DevtoolHelper::pluginAlias($plugin).'\Services\\'.$namespace, '\\');

        $fileSample = file_get_contents($samplePath);

        $fileSample = Str::of($fileSample)
            ->replace('_NAMESPACE', $namespace)
            ->replace('_CLASS_NAME', $filename.'Service')
            ->toString();

        if($storage->put($path, $fileSample))
        {
            $this->line(function (Message $message) use ($file) {
                $message->line('success!', 'green');
                $message->line('services '.$file.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file services thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }

}
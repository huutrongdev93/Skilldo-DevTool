<?php
namespace DevTool\Console;
use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use SkillDo\Cms\Plugin\Plugin;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SkillDo\Support\Path;

class CommandMakeAjaxPlugin extends Command {

    protected string $signature = 'make:ajax:plugin {plugin} {name}';

    protected string $description = 'Generates an Ajax plugin';

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

        $name = $this->argument('name');

        if(!preg_match('/^[A-Za-z0-9\\\\\-\/_]+$/', $name))
        {
            $this->line('Error: Tên file ajax không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ ajax name: '.$name);
            return self::ERROR;
        }

        $plugin = Plugin::get($plugin);

        if(empty($plugin))
        {
            $this->line('Error: plugin not found.');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        return $this->creatFile($plugin, $name);
    }

    public function creatFile($plugin, $file): bool
    {
        $folder = $plugin->getId() . DIRECTORY_SEPARATOR . 'app' .DIRECTORY_SEPARATOR.'Ajax'.DIRECTORY_SEPARATOR;

        [
            'namespace' => $namespace,
            'filename'  => $filename
        ] = DevtoolHelper::getNamespaceAndClass($file);

        $path = $folder.((!empty($namespace)) ? str_replace('\\', '/', $namespace).'/': '').$filename.'Ajax.php';

        if(file_exists(Path::plugin($path)))
        {
            $this->line('Error: file ajax plugin '.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$path);
            return self::ERROR;
        }

        $samplePath = Path::plugin('DevTool/sample/ajax.php');

        if(!file_exists($samplePath))
        {
            $this->line('Error: file ajax sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        $storage = Storage::disk('plugins');

        if(!empty($namespace) && !$storage->exists($folder.DIRECTORY_SEPARATOR.$namespace))
        {
            $storage->makeDirectory(path_normalize($folder.DIRECTORY_SEPARATOR.$namespace));
        }

        $namespace = rtrim(DevtoolHelper::pluginAlias($plugin).'\Ajax\\'.$namespace, '\\');

        $fileSample = file_get_contents($samplePath);

        $fileSample = Str::of($fileSample)
            ->replace('AJAX_NAMESPACE', $namespace)
            ->replace('AJAX_CLASS_NAME', $filename.'Ajax')
            ->toString();

        if($storage->put($path, $fileSample))
        {
            $this->line(function (Message $message) use ($file) {
                $message->line('success!', 'green');
                $message->line('ajax '.$file.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file ajax thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }

}
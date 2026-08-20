<?php
namespace DevTool\Console;
use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SkillDo\Support\Path;

class CommandMakeAjaxTheme extends Command {

    protected string $signature = 'make:ajax:theme {name}';

    protected string $description = 'Generates an Ajax';

    public function handle(): bool
    {
        $name = $this->argument('name');

        if(!preg_match('/^[A-Za-z0-9\\\\\-\/_]+$/', $name)) {
            $this->line('Error: Tên file ajax không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ ajax name: '.$name);
            return self::ERROR;
        }

        $folder = config('cms.theme.child'). DIRECTORY_SEPARATOR . 'app' .DIRECTORY_SEPARATOR.'Ajax'.DIRECTORY_SEPARATOR;

        return $this->creatFile($folder, $name);
    }

    public function creatFile($folder, $file): bool
    {
        [
            'namespace' => $namespace,
            'filename'  => $filename
        ] = DevtoolHelper::getNamespaceAndClass($file);

        $path = $folder.((!empty($namespace)) ? str_replace('\\', '/', $namespace).'/': '').$filename.'Ajax.php';

        if(file_exists('views/'.$path))
        {
            $this->line('Error: file ajax views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
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

        $storage = Storage::disk('views');

        if(!empty($namespace) && !$storage->exists($folder.DIRECTORY_SEPARATOR.$namespace))
        {
            $storage->makeDirectory(path_normalize($folder.DIRECTORY_SEPARATOR.$namespace));
        }

        $namespace = rtrim('Theme\Ajax\\'.$namespace, '\\');

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
<?php
namespace DevTool\Console;
use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use Illuminate\Support\Facades\Storage;
use SkillDo\Support\Path;
use Str;

class CommandMakeModuleTheme extends Command {

    protected string $signature = 'make:module:theme {module} {table}';

    protected string $description = 'Generates an module';

    public function handle(): bool
    {
        $module = $this->argument('module');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', $module))
        {
            $this->line('Error: Tên module không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ module name: '.$module);
            return self::ERROR;
        }

        $table = $this->argument('table');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', $table))
        {
            $this->line('Error: Tên table không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ table name: '.$table);
            return self::ERROR;
        }

        $folder = config('cms.theme.child');

        return $this->creatFile($folder, $module, $table);
    }

    public function creatFile($folder, $module, $table): bool
    {
        $storage = Storage::disk('views');

        $moduleClassName = Str::studly($module);

        $module = Str::lower($module);

        $paths = [
            'table'      => $folder.'/app/Modules/Admin/'.$moduleClassName.'/'.$moduleClassName.'Table.php',
            'form'       => $folder.'/app/Modules/Admin/'.$moduleClassName.'/'.$moduleClassName.'Form.php',
            'service'    => $folder.'/app/Modules/Admin/'.$moduleClassName.'/'.$moduleClassName.'Service.php',
            'model'      => $folder.'/app/Models/'.$moduleClassName.'.php',
            'controller' => $folder.'/app/Controllers/Admin/'.$moduleClassName.'Controller.php',
            'bootstrap'  => $folder.'/bootstrap/'.$module.'.php',
            'route'      => $folder.'/routes/admin-'.$module.'.php'
        ];

        foreach($paths as $path)
        {
            if($storage->has($path))
            {
                $this->line('Error: file views/'.$path.' is exits.');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ views/'.$path);
                return self::ERROR;
            }
        }

        if(!$storage->has($folder.'/app/Modules/Admin/'.$moduleClassName) && !$storage->makeDirectory($folder.'/app/Modules/Admin/'.$moduleClassName))
        {
            $this->line('Error: không tạo được thư mục module');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        foreach(array_keys($paths) as $key)
        {
            $samplePath = Path::plugin("DevTool/sample/module/{$key}.php");

            if(!file_exists($samplePath))
            {
                $this->callback($folder.'/app/Modules/Admin/'.$moduleClassName, $paths, $storage);
                $this->line('Error: file module sample not found.');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ '.$samplePath);
                return self::ERROR;
            }
        }

        foreach($paths as $key => $path)
        {
            $content = file_get_contents(Path::plugin("DevTool/sample/module/{$key}.php"));

            $content = Str::of($content)
                ->replace('NAMESPACE_CLASS_NAME', 'Theme')
                ->replace('MODULE_CLASS_NAME', $moduleClassName)
                ->replace('MODULE_MODEL_TABLE', $table)
                ->replace('MODULE_KEY', $module)
                ->toString();

            if(!$storage->put($path, $content))
            {
                $this->callback($folder.'/app/Modules/Admin/'.$moduleClassName, $paths, $storage);
                $this->line('Error: file views/'.$path.' not make.');
                $this->line('+ '.$this->fullCommand());
                return self::ERROR;
            }
        }

        /*
         * Lõi chỉ nạp routes/admin.php nên phải require file route của module vào đó
         */
        if(!DevtoolHelper::linkAdminRouteFile($storage, $folder, 'admin-'.$module.'.php'))
        {
            $this->callback($folder.'/app/Modules/Admin/'.$moduleClassName, $paths, $storage);
            $this->line('Error: không ghi được vào views/'.$folder.'/routes/admin.php');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        $this->line(function (Message $message) use ($module) {
            $message->line('success!', 'green');
            $message->line('module '.$module.' is created');
        });

        return self::SUCCESS;
    }

    protected function callback($module, $paths, $storage): void
    {
        foreach($paths as $path)
        {
            if($storage->has($path))
            {
                $storage->delete($path);
            }
        }

        $storage->deleteDirectory($module);
    }
}

<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use SkillDo\Support\Path;
use Storage;
use Str;

class CommandMakePlugin extends Command
{
    protected string $signature = 'make:plugin {folder}';

    protected string $description = 'Create new plugin folder';

    public function handle(): bool
    {
        $folderName = $this->argument('folder');

        $folderName = Str::slug($folderName);

        $storage = Storage::disk('plugins');

        if($storage->exists($folderName))
        {
            $this->line('Error: plugin folder '.$folderName.' is exits.');
            $this->line($this->fullCommand());
            $this->line('folder name: '.$folderName);
            return self::ERROR;
        }

        $samplePaths = [
            'index.php'            => Path::plugin('DevTool/sample/plugin/index.php'),
            'plugin.json'          => Path::plugin('DevTool/sample/plugin/plugin.json'),
            'ActivatorService.php' => Path::plugin('DevTool/sample/plugin/ActivatorService.php'),
            'DeactivatorService.php' => Path::plugin('DevTool/sample/plugin/DeactivatorService.php'),
        ];

        foreach($samplePaths as $samplePath)
        {
            if(!file_exists($samplePath))
            {
                $this->line('Error: file plugin sample not found.');
                $this->line($this->fullCommand());
                $this->line('+ '.$samplePath);
                return self::ERROR;
            }
        }

        //app - các thư mục được lõi tự map PSR-4 (SkillDo\Cms\Loader::resolvePrefixesPsr4)
        $storage->makeDirectory($folderName.'/app/Ajax');
        $storage->makeDirectory($folderName.'/app/Cms');
        $storage->makeDirectory($folderName.'/app/Controllers');
        $storage->makeDirectory($folderName.'/app/Middlewares');
        $storage->makeDirectory($folderName.'/app/Models');
        $storage->makeDirectory($folderName.'/app/Modules');
        $storage->makeDirectory($folderName.'/app/Providers');
        $storage->makeDirectory($folderName.'/app/Services');
        $storage->makeDirectory($folderName.'/app/Supports');
        //app - thư mục được preload (không qua PSR-4)
        $storage->makeDirectory($folderName.'/app/Macros');
        $storage->makeDirectory($folderName.'/app/helpers');
        //default
        $storage->makeDirectory($folderName.'/assets/js');
        $storage->makeDirectory($folderName.'/assets/css');
        $storage->makeDirectory($folderName.'/assets/images');
        $storage->makeDirectory($folderName.'/database');
        $storage->makeDirectory($folderName.'/views');
        $storage->makeDirectory($folderName.'/language/en');
        $storage->makeDirectory($folderName.'/language/vi');
        $storage->put($folderName.'/bootstrap/ajax.php', "<?php\n");
        $storage->put($folderName.'/bootstrap/config.php', "<?php\n");
        /*
         * Lõi chỉ nạp đúng 3 file route này của plugin
         * (SkillDo\Cms\Providers\CmsRouteServiceProvider)
         */
        $storage->put($folderName.'/routes/admin.php', "<?php\n\nuse SkillDo\\Support\\Facades\\Route;\n");
        $storage->put($folderName.'/routes/web.php', "<?php\n\nuse SkillDo\\Support\\Facades\\Route;\n");

        //index.php
        $pluginClassName = Str::studly($folderName);

        $fileIndexContent = Str::of($storage->get('DevTool/sample/plugin/index.php'))
            ->replace('PLUGIN_CLASS_NAME', $pluginClassName)
            ->replace('PLUGIN_NAME', $folderName)
            ->toString();

        $storage->put($folderName.'/index.php', $fileIndexContent);

        //plugin.json
        $fileIndexContent = Str::of($storage->get('DevTool/sample/plugin/plugin.json'))
            ->replace('PLUGIN_CLASS_NAME', $pluginClassName)
            ->replace('PLUGIN_NAME', $folderName)
            ->toString();

        $storage->put($folderName.'/plugin.json', $fileIndexContent);

        //ActivatorService
        $fileIndexContent = Str::of($storage->get('DevTool/sample/plugin/ActivatorService.php'))
            ->replace('PLUGIN_CLASS_NAME', $pluginClassName)
            ->replace('PLUGIN_NAME', $folderName)
            ->toString();

        $storage->put($folderName.'/app/Services/ActivatorService.php', $fileIndexContent);

        //DeactivatorService
        $fileIndexContent = Str::of($storage->get('DevTool/sample/plugin/DeactivatorService.php'))
            ->replace('PLUGIN_CLASS_NAME', $pluginClassName)
            ->replace('PLUGIN_NAME', $folderName)
            ->toString();

        $storage->put($folderName.'/app/Services/DeactivatorService.php', $fileIndexContent);

        $this->line(function (Message $message) use ($pluginClassName) {
            $message->line('success!', 'green');
            $message->line('plugin');
            $message->line($pluginClassName, 'blue');
            $message->line('is created');
        });

        return self::SUCCESS;
    }
}

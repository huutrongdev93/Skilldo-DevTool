<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use JetBrains\PhpStorm\NoReturn;
use SkillDo\DevTool\Commands\Message;
use Str;

class CommandMakePlugin extends Command
{
    protected string $signature = 'make:plugin {folder}';

    protected string $description = 'Create new plugin folder';

    #[NoReturn]
    public function handle(): bool
    {
        $folderName = $this->argument('folder');

        $folderName = Str::slug($folderName);

        $folder = 'plugins/'.$folderName;

        $storage = \Storage::disk('plugin');

        if($storage->exists($folderName)) {
            $this->line('Error: plugin folder '.$folderName.' is exits.');
            $this->line($this->fullCommand());
            $this->line('folder name: '.$folderName);
            return self::ERROR;
        }

        $samplePath = 'plugins/DevTool/sample/plugin/index.php';

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file plugin sample not found.');
            $this->line($this->fullCommand());
            $this->line('+ views/'.$samplePath);
            return self::ERROR;
        }

        if(!file_exists('views/'.$folder)) {
            mkdir('views/'.$folder, 0775);
            mkdir('views/'.$folder.'/assets', 0775);
            mkdir('views/'.$folder.'/assets/js', 0775);
            mkdir('views/'.$folder.'/assets/css', 0775);
            mkdir('views/'.$folder.'/assets/images', 0775);
            mkdir('views/'.$folder.'/views', 0775);
            mkdir('views/'.$folder.'/language', 0775);
            mkdir('views/'.$folder.'/language/vi', 0775);
            mkdir('views/'.$folder.'/language/en', 0775);
        }

        //index.php
        $pluginClassName = ucwords(str_replace('-', '_', $folderName));

        $fileIndexContent = $storage->get('DevTool/sample/plugin/index.php');

        $fileIndexContent = str_replace('{{PLUGIN_CLASS_NAME}}', $pluginClassName, $fileIndexContent);

        $fileIndexContent = str_replace('{{PLUGIN_NAME}}', $folderName, $fileIndexContent);

        $storage->put($folderName.'/index.php', $fileIndexContent);

        //plugin.json
        $fileIndexContent = $storage->get('DevTool/sample/plugin/plugin.json');

        $fileIndexContent = str_replace('{{PLUGIN_CLASS_NAME}}', $pluginClassName, $fileIndexContent);

        $fileIndexContent = str_replace('{{PLUGIN_NAME}}', $folderName, $fileIndexContent);

        $storage->put($folderName.'/plugin.json', $fileIndexContent);

        $this->line(function (Message $message) use ($pluginClassName) {
            $message->line('success!', 'green');
            $message->line('plugin');
            $message->line($pluginClassName, 'blue');
            $message->line('is created');
        });

        return self::SUCCESS;
    }
}
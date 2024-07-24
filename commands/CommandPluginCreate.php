<?php
namespace SkillDo\DevTool\Commands;

use JetBrains\PhpStorm\NoReturn;
use Str;

class CommandPluginCreate extends Command {

    public function paramCheck(): bool
    {
        return true;
    }

    #[NoReturn]
    public function run(): void
    {
        $folderName = trim($this->params[0]);

        $folderName = Str::slug($folderName);

        $folder = 'plugins/'.$folderName;

        $storage = \Storage::disk('plugin');

        if($storage->exists($folderName)) {
            response()->error('Error: plugin folder '.$folderName.' is exits. ', [
                '+ '.$this->fullCommand(),
            ]);
        }

        $samplePath = 'plugins/DevTool/sample/plugin/index.php';

        if(!file_exists('views/'.$samplePath)) {
            response()->error('Error: file plugin sample not found.', [
                '+ '.$this->fullCommand(),
                '+ views/'.$samplePath,
            ]);
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

        response()->success('[[;#00ee11;]success!] plugin '.$pluginClassName.' is created [[;blue;]]');
    }
}
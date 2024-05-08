<?php
namespace SkillDo\DevTool\Commands;

use JetBrains\PhpStorm\NoReturn;

class CommandPluginDbCreate extends Command {

    public function paramCheck(): bool
    {
        if(count($this->params) != 2) {
            return false;
        }
        return true;
    }

    #[NoReturn]
    public function run(): void
    {
        $folder = 'plugins/'.trim($this->params[0]);

        $file = trim($this->params[1]);

        $file = \Str::ascii($file);

        $path = $folder.'/database/'.$file.'.php';

        $samplePath = 'plugins/DevTool/sample/database.php';

        if(file_exists('views/'.$path)) {
            response()->error('Error: file database views/'.$path.' is exits. ', [
                '+ '.$this->fullCommand(),
                '+ views/'.$path,
            ]);
        }

        if(!file_exists('views/'.$samplePath)) {
            response()->error('Error: file database sample not found. ', [
                '+ '.$this->fullCommand(),
                '+ views/'.$samplePath,
            ]);
        }

        $storage = \Storage::disk('views');

        if(!file_exists('views/'.$folder.'/database')) {
            mkdir('views/'.$folder.'/database', 0775);
        }

        $storage->copy($samplePath, $path);

        response()->success('[[;#00ee11;]success!] file is created [[;blue;]'.$file.']');
    }
}
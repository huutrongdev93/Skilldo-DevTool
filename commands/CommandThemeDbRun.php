<?php
namespace SkillDo\DevTool\Commands;

class CommandThemeDbRun extends Command {

    public function paramCheck(): bool
    {
        if($this->paramsCount > 1) {
            return false;
        }
        return true;
    }

    public function run(): void
    {
        $folder = 'views/'.\Theme::name();

        $file = !empty($this->params[0]) ? $this->params[0] : 'database';

        $path = $folder.'/database/'.$file.'.php';

        if(!file_exists($path)) {

            response()->error('Error: file database '.$path.' is not found. ', [
                '+ '.$this->fullCommand(),
                '+ '.$path,
            ]);
        }

        try {

            $database = include_once $path;

            $database->up();

            response()->success('[[;#00ee11;]success!]');
        }
        catch(\Exception $e) {

            response()->error($e->getMessage());
        }
    }
}
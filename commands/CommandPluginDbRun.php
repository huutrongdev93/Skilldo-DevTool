<?php
namespace SkillDo\DevTool\Commands;

class CommandPluginDbRun extends Command {

    public function paramCheck(): bool
    {
        if($this->paramsCount > 2 || $this->paramsCount == 0) {
            return false;
        }
        return true;
    }

    public function run(): void
    {
        $folder = 'views/plugins/'.trim($this->params[0]);

        $file = !empty($this->params[1]) ? $this->params[1] : 'database';

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
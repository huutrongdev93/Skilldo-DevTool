<?php
namespace SkillDo\DevTool\Commands;

class Command {

    protected string $command;

    protected array $params = [];

    protected int $paramsCount = 0;

    protected string $status = 'error';

    protected array $data = [];

    protected string $message = '';

    public function __construct($command) {

        $this->command = $command[0];

        unset($command[0]);

        if(have_posts($command)) {

            $this->params = array_values($command);

            $this->paramsCount = count($command);
        }
    }

    public function paramCheck(): bool
    {
        return true;
    }

    public function fullCommand(): string
    {
        return $this->command.' '.implode(' ', $this->params);
    }

    public function response(): void
    {
        if($this->status == 'error') {
            if(empty($this->message)) {
                $this->message = $this->command.' : The term \''.$this->command.'\' is not recognized as a valid command. Check spelling of the name, or if a path is included, verify that the path is correct and try again.';
                $this->data[]  = '+ '.$this->fullCommand();
            }
            response()->error($this->message, $this->data);
        }

        if($this->status == 'success') {
            response()->success($this->message, $this->data);
        }
    }

    static function make($command): mixed
    {
        //cms command
        if($command[0] == 'cms:version') {
            return new CommandCmsVersion($command);
        }

        if($command[0] == 'cms:lang:build') {
            return new CommandCmsLangBuild($command);
        }

        if($command[0] == 'cache:clear') {
            return new CommandCacheClear($command);
        }

        //theme command
        if($command[0] == 'theme:db:run') {
            return new CommandThemeDbRun($command);
        }

        //command database create
        if($command[0] == 'theme:db:create') {
            return new CommandThemeDbCreate($command);
        }

        if($command[0] == 'theme:child:copy') {
            return new CommandThemeChildCopy($command);
        }

        //plugin command
        if($command[0] == 'pl' || $command[0] == 'plugin') {
            return new CommandPluginList($command);
        }

        //command database run
        if($command[0] == 'plugin:db:run') {
            return new CommandPluginDbRun($command);
        }

        //command database create
        if($command[0] == 'plugin:db:create') {
            return new CommandPluginDbCreate($command);
        }

        //command database show
        if($command[0] == 'db:show') {
            return new CommandDbShow($command);
        }

        if($command[0] == 'db:table') {
            return new CommandDbTable($command);
        }

        return false;
    }
}
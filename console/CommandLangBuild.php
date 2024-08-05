<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;

class CommandLangBuild extends Command
{
    protected string $signature = 'lang:build {type}';

    protected string $description = 'Build file json language';

    public function handle(): bool
    {
        $type = $this->argument('type');

        if($type == 'js') {

            \Language::buildJs();

            return self::SUCCESS;
        }

        return self::ERROR;
    }
}
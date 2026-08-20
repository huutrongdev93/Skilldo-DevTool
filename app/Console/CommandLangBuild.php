<?php
namespace DevTool\Console;
use DevTool\Commands\Command;
use SkillDo\Cms\Support\Language;

class CommandLangBuild extends Command
{
    protected string $signature = 'lang:build {type}';

    protected string $description = 'Build file json language';

    public function handle(): bool
    {
        $type = $this->argument('type');

        if($type == 'js')
        {
            Language::buildJs();

            return self::SUCCESS;
        }

        return self::ERROR;
    }
}
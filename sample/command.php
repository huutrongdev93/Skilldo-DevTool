<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;

class CommandClassName extends Command {

    protected string $signature = '';

    protected string $description = '';

    public function handle(): bool
    {
        return self::SUCCESS;
    }
}
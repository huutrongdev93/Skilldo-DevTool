<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;

class CommandAuthLogout extends Command {

    protected string $signature = 'auth:logout';

    protected string $description = 'User currently logged out';

    public function handle(): bool
    {
        \Auth::logout();

        $this->line('Logged out successfully');

        return self::SUCCESS;
    }
}
<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use SkillDo\Support\Auth;

class CommandAuthLogout extends Command {

    protected string $signature = 'auth:logout';

    protected string $description = 'User currently logged out';

    public function handle(): bool
    {
        Auth::logout();

        $this->line('Logged out successfully');

        return self::SUCCESS;
    }
}
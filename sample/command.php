<?php
namespace DevTool\Console;

use DevTool\Commands\Command;

class CommandClassName extends Command {

    protected string $signature = '';

    protected string $description = '';

    public function handle(): bool
    {
        return self::SUCCESS;
    }
}
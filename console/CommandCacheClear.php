<?php
namespace SkillDo\DevTool\Console;

use JetBrains\PhpStorm\NoReturn;

class CommandCacheClear extends \SkillDo\DevTool\Commands\Command
{
    protected string $signature = 'cache:clear';

    protected string $description = 'Clear all cache files';

    #[NoReturn]
    public function handle(): bool
    {
        \SkillDo\Cache::flush();

        $this->line('success!', 'green');

        return self::SUCCESS;
    }
}
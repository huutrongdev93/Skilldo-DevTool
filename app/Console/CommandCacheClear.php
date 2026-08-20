<?php
namespace DevTool\Console;

use SkillDo\Cache\Cache;

class CommandCacheClear extends \DevTool\Commands\Command
{
    protected string $signature = 'cache:clear';

    protected string $description = 'Clear all cache files';

    public function handle(): bool
    {
        Cache::flush();

        cmsClearCache();

        $this->line('success!', 'green');

        return self::SUCCESS;
    }
}
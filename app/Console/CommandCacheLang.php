<?php
namespace DevTool\Console;

use SkillDo\Cache\Cache;

class CommandCacheLang extends \DevTool\Commands\Command
{
    protected string $signature = 'cache:lang';

    protected string $description = 'Clear all cache languages';

    public function handle(): bool
    {
        // Clear translation caches (path maps + loaded strings per locale/namespace)
        \SkillDo\Translation\Translation::clearCache();

        $this->line('success!', 'green');

        return self::SUCCESS;
    }
}
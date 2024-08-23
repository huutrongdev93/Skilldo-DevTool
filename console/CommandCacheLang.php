<?php
namespace SkillDo\DevTool\Console;

use JetBrains\PhpStorm\NoReturn;

class CommandCacheLang extends \SkillDo\DevTool\Commands\Command
{
    protected string $signature = 'cache:lang';

    protected string $description = 'Clear all cache languages';

    #[NoReturn]
    public function handle(): bool
    {
        \SkillDo\Cache::delete('core_language', true);
        \SkillDo\Cache::delete('language_', true);
        \SkillDo\Cache::delete('widget_load_translations_', true);

        $this->line('success!', 'green');

        return self::SUCCESS;
    }
}
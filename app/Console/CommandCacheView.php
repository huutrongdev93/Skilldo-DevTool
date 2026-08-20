<?php
namespace DevTool\Console;



use SkillDo\Support\Path;

class CommandCacheView extends \DevTool\Commands\Command
{
    protected string $signature = 'cache:view';

    protected string $description = 'Remove an item from the cache views';

    public function handle(): bool
    {
        $listCache = scandir(Path::storage('framework/views'));

        foreach ($listCache as $file)
        {
            if($file == '.') continue;
            if($file == '..') continue;
            unlink( Path::storage('framework/views').DIRECTORY_SEPARATOR.$file);
        }

        $this->line(function (\DevTool\Commands\Message $message) {
            $message->green('success!');
            $message->line('cache view is clear!');
        });

        return self::SUCCESS;
    }
}
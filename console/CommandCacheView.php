<?php
namespace SkillDo\DevTool\Console;

use JetBrains\PhpStorm\NoReturn;

class CommandCacheView extends \SkillDo\DevTool\Commands\Command
{
    protected string $signature = 'cache:view';

    protected string $description = 'Remove an item from the cache views';

    #[NoReturn]
    public function handle(): bool
    {
        $listCache = scandir(VIEWPATH.'cache/views');

        foreach ($listCache as $file) {
            if( $file == '.' ) continue;
            if( $file == '..' ) continue;
            unlink( VIEWPATH.'cache/views/'.$file);
        }

        $this->line(function (\SkillDo\DevTool\Commands\Message $message) {
            $message->green('success!');
            $message->line('cache view is clear!');
        });

        return self::SUCCESS;
    }
}
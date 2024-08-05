<?php
namespace SkillDo\DevTool\Console;
use SKDService;
use SkillDo\DevTool\Commands\Command;
use Str;

class CommandCmsVersion extends Command
{
    protected string $signature = 'cms:version {type=current}';

    protected string $description = 'Display version cms';

    public function handle(): bool
    {
        $type = $this->argument('type');

        $type = Str::lower($type);

        if($type == 'current') {

            $this->line(\Cms::version(), 'green');

            return self::SUCCESS;
        }

        if($type == 'last') {

            $this->line(SKDService::cms()->version(), 'green');

            return self::SUCCESS;
        }

        return self::ERROR;
    }
}
<?php
namespace SkillDo\DevTool\Console;
use Option;
use SkillDo\DB;
use SkillDo\DevTool\Commands\Command;

class CommandLicense extends Command
{
    protected string $signature = 'license';

    protected string $description = 'Show license information';

    public function handle(): bool
    {
        $key = Option::get('api_user');

        $secretKey = Option::get('api_secret_key');

        $this->line('Key :'.$key, 'green');

        $this->line('Secret Key :'.$secretKey, 'green');

        return self::SUCCESS;
    }
}
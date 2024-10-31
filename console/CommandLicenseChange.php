<?php
namespace SkillDo\DevTool\Console;
use Option;
use SkillDo\DB;
use SkillDo\DevTool\Commands\Command;

class CommandLicenseChange extends Command
{
    protected string $signature = 'license:change {key} {secretKey}';

    protected string $description = 'Change license information';

    public function handle(): bool
    {
        $key = $this->argument('key');

        if(empty($key)) {
            $this->line('Error: thông tin key không được để trống');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ key: '.$key);
            return self::ERROR;
        }

        $secretKey = $this->argument('secretKey');

        if(empty($secretKey)) {
            $this->line('Error: thông tin secret key không được để trống');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ key: '.$secretKey);
            return self::ERROR;
        }

        Option::update('api_user', 		$key);

        Option::update('api_secret_key', $secretKey);

        $this->line('Thay đổi license thành công', 'green');

        return self::SUCCESS;
    }
}
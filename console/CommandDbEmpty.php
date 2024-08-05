<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;

class CommandDbEmpty extends Command {

    protected string $signature = 'db:empty {table}';

    protected string $description = 'Empty table, view, and type';

    public function handle(): bool
    {
        $table = $this->argument('table');

        if(!preg_match('/^[a-zA-Z0-9]+$/', $table)) {
            $this->line('Error: Tên table không hợp lệ');
            $this->line($this->fullCommand());
            $this->line('Table: '.$table);
            return self::ERROR;
        }
        try {

            model($table)->truncate();

            $this->line('Empty table success');

            return self::SUCCESS;
        }
        catch (\Exception $e) {

            $this->line($e->getMessage());

            return self::ERROR;
        }

    }
}
<?php
namespace SkillDo\DevTool\Console;
use Role;
use SkillDo\DevTool\Commands\Command;

class CommandRoleCap extends Command {

    protected string $signature = 'role:cap {role}';

    protected string $description = 'Show a list of capabilities for a specific role';

    public function handle(): bool
    {
        $roleName = $this->argument('role');

        if(!preg_match('/^[a-zA-Z0-9-]+$/', $roleName)) {
            $this->line('Error: Tên role không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ role name: '.$roleName);
            return self::ERROR;
        }

        $caps = Role::get($roleName)->getCapabilities();

        $this->line('Danh sách quyền cho role: '.$roleName);

        if(empty($caps)) {
            $this->success('Role chưa được phân quyền nào');
        }

        foreach ($caps as $key => $name) {
            $this->line($this->renderLine([
                'start' => $key,
                'end' => $name ? '[[;#00ee11;]true]' : 'false',
            ]));
        }

        return self::SUCCESS;
    }

    public function renderLine($args): string
    {
        $start = $args['start'];

        $color = $args['color'] ?? '';

        $end = $args['end'] ?? '';

        $startLength = strlen($start);

        $endLength = strlen($end);

        $dot = 40 - $startLength - $endLength;

        $line = (!empty($color)) ? '['.$color.$start.']' : $start;

        $line .= str_repeat('.', $dot + 1);

        $line .= $end;

        return $line;
    }
}
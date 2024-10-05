<?php
namespace SkillDo\DevTool\Console;
use Role;
use SkillDo\DevTool\Commands\Command;

class CommandRole extends Command {

    protected string $signature = 'role:list';

    protected string $description = 'Show a list of all roles';

    public function handle(): bool
    {
        $rolesName = Role::make()->getNames();

        foreach ($rolesName as $key => $name) {

            $this->line($this->renderLine([
                'start' => $key,
                'end' => $name,
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
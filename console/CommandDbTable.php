<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;

class CommandDbTable extends Command
{
    protected string $signature = 'db:table {table}';

    protected string $description = 'Display information about the given database table';

    public function handle(): bool
    {
        $table = $this->argument('table');

        if(!schema()->hasTable($table))
        {
            $this->line('Table ' . $table . ' not found');

            return self::ERROR;
        }

        $columns = schema()->getColumns($table);

        $this->line($this->renderLine([
            'start' => $table,
            'color' => '[b;#00ee11;]'
        ]));

        $this->line($this->renderLine([
            'start' => 'Columns',
            'end' => count($columns)
        ]));

        $this->line($this->renderLine([
            'start' => 'Size',
            'end' => 0
        ]));

        $this->line($this->renderLine([
            'start' => 'Column',
            'color' => '[b;#00ee11;]',
            'end' => 'Type'
        ]));

        foreach ($columns as $column) {

            $this->line($this->renderLine([
                'start' => $column['name'],
                'end' => $column['type_name'],
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

        $dot = 50 - $startLength - $endLength;

        $line = (!empty($color)) ? '['.$color.$start.']' : $start;

        $line .= str_repeat('.', $dot + 1);

        $line .= $end;

        return $line;
    }
}
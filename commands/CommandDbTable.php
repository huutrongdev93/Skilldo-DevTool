<?php
namespace SkillDo\DevTool\Commands;
use Illuminate\Database\Capsule\Manager as DB;

class CommandDbTable extends Command {

    public function paramCheck(): bool
    {
        if(count($this->params) != 1) {
            return false;
        }
        return true;
    }

    public function run(): void
    {
        if(!schema()->hasTable($this->params[0]))
        {
            response()->error('Table ' . $this->params[0] . ' not found');
        }

        $columns = schema()->getColumns($this->params[0]);

        $this->message = $this->line([
            'start' => $this->params[0],
            'color' => '[b;#00ee11;]'
        ]);

        $lines = [];

        $lines[0] = $this->line([
            'start' => 'Columns',
            'end' => count($columns)
        ]);

        $lines[1] = $this->line([
            'start' => 'Size',
            'end' => 0
        ]);

        $lines[2] = $this->line([
            'start' => 'Column',
            'color' => '[b;#00ee11;]',
            'end' => 'Type'
        ]);

        foreach ($columns as $column) {

            $lines[] = $this->line([
                'start' => $column['name'],
                'end' => $column['type_name'],
            ]);
        }

        $this->status = 'success';

        $this->data = $lines;

        $this->response();
    }

    public function line($args): string
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
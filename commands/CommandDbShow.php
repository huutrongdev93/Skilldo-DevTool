<?php
namespace SkillDo\DevTool\Commands;
use Illuminate\Database\Capsule\Manager as DB;

class CommandDbShow extends Command {

    public function paramCheck(): bool
    {
        if(count($this->params) > 0) {
            return false;
        }
        return true;
    }

    public function run(): void
    {

        $lines = [];

        $tables = DB::getSchemaBuilder()->getTables();

        $totalSize = 0;

        $output = model()->query("SHOW VARIABLES LIKE 'version'");

        $lines[0] = $this->line([
            'start' => 'Database',
            'end' => CLE_DBNAME
        ]);

        $lines[1] = $this->line([
            'start' => 'Host',
            'end' => CLE_DBHOST
        ]);

        $lines[2] = $this->line([
            'start' => 'Port',
            'end' => 3306
        ]);

        $lines[3] = $this->line([
            'start' => 'Tables',
            'end' => count($tables)
        ]);

        $lines[4] = '';

        $lines[5] = $this->line([
            'start' => 'Table',
            'color' => '[b;#00ee11;]',
            'end' => 'Size (MiB)'
        ]);

        foreach ($tables as $table) {

            $totalSize += $table['size'];

            $lines[] = $this->line([
                'start' => $table['name'],
                'end' => round($table['size']/(1024 * 1024), 2).' MiB',
            ]);
        }

        $lines[4] = $this->line([
            'start' => 'Total Size',
            'end' => round($totalSize/(1024 * 1024), 2).' MiB',
        ]);

        $this->status = 'success';

        $this->message = $this->line([
            'start' => 'MySQL '.$output[0]->Value,
            'color' => '[b;#00ee11;]'
        ]);

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
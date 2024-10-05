<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DB;
use SkillDo\DevTool\Commands\Command;

class CommandDbShow extends Command
{
    protected string $signature = 'db:show';

    protected string $description = 'Display information about the given database';

    public function handle(): bool
    {
        $tables = DB::getSchemaBuilder()->getTables();

        $totalSize = 0;

        $output = DB::select("SHOW VARIABLES LIKE 'version'");

        $this->line('MySQL '.$output[0]->Value, 'green');

        $this->line($this->renderLine([
            'start' => 'Database',
            'end' => DB_DATABASE
        ]));

        $this->line($this->renderLine([
            'start' => 'Host',
            'end' => DB_HOST
        ]));

        $this->line($this->renderLine([
            'start' => 'Port',
            'end' => 3306
        ]));

        $this->line($this->renderLine([
            'start' => 'Tables',
            'end' => count($tables)
        ]));

        foreach ($tables as $table) {
            $totalSize += $table['size'];
        }

        $this->line($this->renderLine([
            'start' => 'Total Size',
            'end' => round($totalSize/(1024 * 1024), 2).' MiB',
        ]));

        $this->line($this->renderLine([
            'start' => 'Table',
            'color' => '[b;#00ee11;]',
            'end' => 'Size (MiB)'
        ]));

        foreach ($tables as $table) {

            $this->line($this->renderLine([
                'start' => $table['name'],
                'end' => round($table['size']/(1024 * 1024), 2).' MiB',
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
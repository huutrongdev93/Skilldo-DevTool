<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use Str;

class CommandHelpList extends Command {

    protected string $signature = 'help';

    protected string $description = 'List commands';

    public function handle(): bool
    {
        $storage = \Storage::disk('plugin');

        $consoles = $storage->files('DevTool/console');

        foreach ($consoles as $console) {
            include_once 'views/plugins/'.$console;
        }

        $consolesClass = [];

        $classes = get_declared_classes();

        foreach ($classes as $class) {
            if(Str::startsWith($class, 'SkillDo\DevTool\Console\\')) {
                $consolesClass[] = new $class();
            }
        }

        $consoles = [];

        $availableConsoles = [];

        if(have_posts($consolesClass)) {
            foreach ($consolesClass as $class) {
                $command = $class->signature();
                $command = explode(' ', $command);
                $groups  = explode(':', $command[0]);
                if(count($groups) == 1) {
                    $availableConsoles[$command[0]] = $class->getDescription();
                }
                else {
                    $consoles[$groups[0]][$command[0]] = $class->getDescription();
                }
            }

            if(have_posts($availableConsoles)) {
                foreach ($availableConsoles as $key => $class) {
                    if(!empty($consoles[$key])) {
                        $consoles[$key][$key] = $class;
                        unset($availableConsoles[$key]);
                        ksort($consoles[$key]);
                    }
                }
            }
        }

        ksort($consoles);

        array_unshift($consoles, $availableConsoles);

        foreach ($consoles as $groupName => $consoleGroup) {
            if($groupName == 0) {
                $groupName = 'Available commands';
            }
            $this->messages->line($groupName, 'yellow', true);
            foreach ($consoleGroup as $command => $description) {
                $this->line($this->renderLine([
                    'start' => '   '.$command,
                    'color' => '[;#00ee11;]',
                    'end' => $description
                ]));
            }
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

        $dot = 50 - $startLength;

        $line = (!empty($color)) ? '['.$color.$start.']' : $start;

        $line .= str_repeat(' ', $dot + 1);

        $line .= $end;

        return $line;
    }
}
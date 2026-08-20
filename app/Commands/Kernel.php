<?php
namespace DevTool\Commands;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Illuminate\Support\Str;

class Kernel
{
    protected string $commandPath;

    protected string $commandNamespace = 'DevTool\\Console';

    public function __construct(string $commandPath)
    {
        $this->commandPath = rtrim($commandPath, '/');
    }

    public function run($command): void
    {
        $console = explode(' ', $command);

        $console = array_filter($console, fn($value) => !is_null($value) && $value !== '');

        if(empty($console[0]))
        {
            response()->error('Error: command is empty!');
        }

        $commands = $this->loadCommands();

        $terminal = false;

        if(hasItems($commands))
        {
            foreach ($commands as $class)
            {
                if(Str::startsWith($class, $this->commandNamespace))
                {
                    $consoleObject = new $class($command);
                    $signature = $consoleObject->signature();
                    $signature = explode(' ', $signature);
                    if(!empty($signature[0]) && $signature[0] == $console[0])
                    {
                        $terminal = $consoleObject;
                        break;
                    }
                }
            }
        }

        if($terminal === false)
        {
            response()->error($console[0].' : The term \''.$console[0].'\' is not recognized as a valid command. Check spelling of the name, or if a path is included, verify that the path is correct and try again.', [
                '+ '.$command
            ]);
        }

        $terminal->run();
    }

    protected function loadCommands(): array
    {
        $classes = [];

        $dir = new RecursiveDirectoryIterator($this->commandPath);

        $it = new RecursiveIteratorIterator($dir);

        foreach ($it as $file)
        {
            if ($file->getExtension() !== 'php') continue;

            $path = str_replace([$this->commandPath, '/', '.php'], ['', '\\', ''], $file->getPathname());

            $class = $this->commandNamespace . $path;

            if (class_exists($class))
            {
                $classes[] = $class;
            }
        }

        return $classes;
    }
}
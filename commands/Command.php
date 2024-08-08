<?php
namespace SkillDo\DevTool\Commands;
use JetBrains\PhpStorm\NoReturn;
use Str;

interface CommandInterface
{
    public function run(): void;
}

class Command implements CommandInterface {

    protected string $signature;

    protected string $description;

    protected array $arguments = [];

    protected array $options = [];

    protected string $command;

    protected array $commandParams;

    protected string $commandFull;

    protected SignatureParser $parser;

    protected Message $messages;

    const SUCCESS = true;

    const ERROR = false;

    public function __construct($command = '') {

        if(empty($this->signature)) {
            response()->error('Error: signature is empty. ', [
                '+ '.$command
            ]);
        }

        $this->setCommand($command);

        $this->parser = new SignatureParser($this->signature);

        $this->messages = new Message();
    }

    public function fullCommand(): string
    {
        return $this->commandFull;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setCommand($command): static
    {
        $this->commandFull = $command;

        $this->commandParams = [];

        $this->command = $command;

        if(!empty($command)) {

            $this->commandParams = explode(' ', $command);

            $this->commandParams = array_filter($this->commandParams, fn($value) => !is_null($value) && $value !== '');

            $this->command = strtolower($this->commandParams[0]);

            array_shift($this->commandParams);
        }

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    #[NoReturn]
    public function run(): void
    {
        if (!$this->parser->validateInput($this->commandParams)) {
            response()->error('Error: Invalid input. Please check the command signature. ', [
                '+ '.$this->fullCommand()
            ]);
        }

        $this->parser->parseArguments($this->commandParams);

        $result = $this->handle();

        if($result) {
            $this->success();
        }

        $this->error();
    }

    public function argument($name)
    {
        return $this->parser->getArguments()[$name] ?? null;
    }

    public function signature(): string
    {
        return $this->parser->getSignature();
    }

    public function option($name)
    {
        return $this->parser->getOptions()[$name] ?? null;
    }

    public function handle(): bool
    {
        // Logic của command sẽ được cài đặt trong các class kế thừa
        return self::SUCCESS;
    }

    public function line($message, string $color = ''): static
    {
        if(empty($color)) {
            $this->messages->line($message);
        }
        if($color == 'green') {
            $this->messages->green($message);
        }
        if($color == 'blue') {
            $this->messages->blue($message);
        }
        if($color == 'yellow') {
            $this->messages->blue($message);
        }

        return $this;
    }

    #[NoReturn]
    public function success($message = null): void
    {
        if(!empty($message)) {
            $this->messages->green($message);
        }
        $this->messages->success();
    }

    #[NoReturn]
    public function error($message = null): void
    {
        if(!empty($message)) {
            $this->messages->line($message);
        }
        $this->messages->error();
    }

    #[NoReturn]
    static function make($command): void
    {
        $console = explode(' ', $command);

        $console = array_filter($console, fn($value) => !is_null($value) && $value !== '');

        if(empty($console[0])) {
            response()->error('Error: command is empty!');
        }

        $storage = \Storage::disk('plugin');

        $consolesFile = $storage->files('DevTool/console');

        $terminal = false;

        if(have_posts($consolesFile)) {

            foreach ($consolesFile as $consoleFile) {
                include_once 'views/plugins/'.$consoleFile;
            }

            $classes = get_declared_classes();

            foreach ($classes as $class) {
                if(Str::startsWith($class, 'SkillDo\DevTool\Console\\')) {
                    $consoleObject = new $class($command);
                    $signature = $consoleObject->signature();
                    $signature = explode(' ', $signature);
                    if(!empty($signature[0]) && $signature[0] == $console[0]) {
                        $terminal = $consoleObject;
                        break;
                    }
                }
            }
        }

        if($terminal === false) {
            response()->error($console[0].' : The term \''.$console[0].'\' is not recognized as a valid command. Check spelling of the name, or if a path is included, verify that the path is correct and try again.', [
                '+ '.$command
            ]);
        }

        $terminal->run();
    }
}
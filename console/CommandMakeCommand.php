<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;

class CommandMakeCommand extends Command
{
    protected string $signature = 'make:command {class}';

    protected string $description = 'Create a new Devtool command';

    public function handle(): bool
    {
        $class = $this->argument('class');

        if(!preg_match('/^[a-zA-Z0-9]+$/', $class)) {
            $this->line('Error: tên class command không hợp lệ');
            $this->line($this->fullCommand());
            return self::ERROR;
        }

        $dir = __DIR__.'/'.$class.'.php';

        if(file_exists($dir)) {
            $this->line('Error: file command đã tồn tại');
            $this->line($this->fullCommand());
            return self::ERROR;
        }

        $samplePath = 'plugins/DevTool/sample/command.php';

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file command sample not found');
            $this->line($this->fullCommand());
            $this->line('views/'.$samplePath);
            return self::ERROR;
        }

        $storage = \Storage::disk('plugin');

        $commandSample = $storage->get('DevTool/sample/command.php');

        $commandSample = str_replace('CommandClassName', $class, $commandSample);

        if($storage->put('DevTool/console/'.$class.'.php', $commandSample)) {

            $this->line(function (Message $message) use ($class) {
                $message->line('success!', 'green');
                $message->line('command '.$class.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file command thất bại kiểm tra lại quyền đọc ghi thư mục command');

        return self::ERROR;
    }
}
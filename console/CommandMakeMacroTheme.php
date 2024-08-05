<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;

class CommandMakeMacroTheme extends Command {

    protected string $signature = 'make:macro:theme {class}';

    protected string $description = 'Generates macro class';

    public function handle(): bool
    {
        $file = $this->argument('class');

        if(!preg_match('/^[a-zA-Z0-9]+$/', $file)) {
            $this->line('Error: Tên class không hợp lệ');
            $this->line($this->fullCommand());
            $this->line('class name: '.$file);
            return self::ERROR;
        }

        $folder = \Theme::name();

        return $this->creatFormFile($folder, $file);
    }

    public function creatFormFile($folder, $file): bool
    {
        $path = $folder.'/core/Macro/'.$file.'.php';

        $samplePath = 'plugins/DevTool/sample/core/macro.php';

        if(file_exists('views/'.$path)) {
            $this->line('Error: file macro views/'.$path.' is exits.');
            $this->line($this->fullCommand());
            $this->line('views/'.$path);
            return self::ERROR;
        }

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file macro sample not found.');
            $this->line($this->fullCommand());
            $this->line('views/'.$samplePath);
            return self::ERROR;
        }

        $storage = \Storage::disk('views');

        if(!file_exists('views/'.$folder.'/core')) {
            mkdir('views/'.$folder.'/core', 0775);
        }

        if(!file_exists('views/'.$folder.'/core/Macro')) {
            mkdir('views/'.$folder.'/core/Macro', 0775);
        }

        $fileSample = $storage->get($samplePath);

        $fileSample = str_replace('MacroClassName', $file, $fileSample);

        if($storage->put($path, $fileSample)) {

            $this->line(function (Message $message) use ($file) {
                $message->line('success!', 'green');
                $message->line('Macro '.$file.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file Macro thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }
}
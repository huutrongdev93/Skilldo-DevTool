<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;

class CommandMakePopoverPlugin extends Command {

    protected string $signature = 'make:popover:plugin {plugin} {class}';

    protected string $description = 'Generates form popover class';

    public function handle(): bool
    {
        $plugin = $this->argument('plugin');

        if(!preg_match('/^[a-zA-Z0-9-]+$/', $plugin)) {
            $this->line('Error: tên thư mục plugin không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ plugin: '.$plugin);
            return self::ERROR;
        }

        $file = $this->argument('class');

        if(!preg_match('/^[a-zA-Z0-9.-]+$/', $file)) {
            $this->line('Error: Tên file không hợp lệ');
            $this->line($this->fullCommand());
            $this->line('file name: '.$file);
            return self::ERROR;
        }

        $folder = 'plugins/'.$plugin;

        return $this->creatFormFile($folder, $file);
    }

    public function creatFormFile($folder, $file): bool
    {
        $path = $folder.'/core/Form/Popover/'.$file.'.php';

        $samplePath = 'plugins/DevTool/sample/core/form-popover.php';

        if(file_exists('views/'.$path)) {
            $this->line('Error: file form Popover views/'.$path.' is exits.');
            $this->line($this->fullCommand());
            $this->line('views/'.$path);
            return self::ERROR;
        }

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file form Popover sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$samplePath);
            return self::ERROR;
        }

        $storage = \Storage::disk('views');

        if(!file_exists('views/'.$folder.'/core')) {
            mkdir('views/'.$folder.'/core', 0775);
        }

        if(!file_exists('views/'.$folder.'/core/Form')) {
            mkdir('views/'.$folder.'/core/Form', 0775);
        }

        if(!file_exists('views/'.$folder.'/core/Form/Popover')) {
            mkdir('views/'.$folder.'/core/Form/Popover', 0775);
        }

        $fileSample = $storage->get($samplePath);

        $fileSample = str_replace('FormPopoverClassName', $file, $fileSample);

        if($storage->put($path, $fileSample)) {

            $this->line(function (Message $message) use ($file) {
                $message->line('success!', 'green');
                $message->line('form Popover '.$file.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file form Popover thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }
}
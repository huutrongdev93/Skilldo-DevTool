<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;

class CommandMakeAjax extends Command {

    protected string $signature = 'make:ajax {name}';

    protected string $description = 'Generates an Ajax';

    public function handle(): bool
    {
        $name = $this->argument('name');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', $name)) {
            $this->line('Error: Tên file ajax không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ ajax name: '.$name);
            return self::ERROR;
        }

        $folder = \Theme::name();

        return $this->creatFile($folder, $name);
    }

    public function creatFile($folder, $file): bool
    {
        $path = $folder.'/theme-custom/ajax/'.$file.'.ajax.php';

        if(file_exists('views/'.$path)) {
            $this->line('Error: file ajax views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
            return self::ERROR;
        }

        $samplePath = 'plugins/DevTool/sample/ajax.php';

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file ajax sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$samplePath);
            return self::ERROR;
        }

        $storage = \Storage::disk('views');

        if(!file_exists('views/'.$folder.'/theme-custom/ajax')) {
            mkdir('views/'.$folder.'/theme-custom/ajax', 0775);
        }

        $fileSample = $storage->get($samplePath);

        if($storage->put($path, $fileSample)) {

            $this->line(function (Message $message) use ($file) {
                $message->line('success!', 'green');
                $message->line('ajax '.$file.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file taxonomy thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }

}
<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;

class CommandMakeColumnTheme extends Command {

    protected string $signature = 'make:column:theme {class}';

    protected string $description = 'Generates table column class';

    public function handle(): bool
    {
        $file = $this->argument('class');

        if(!preg_match('/^[a-zA-Z0-9]+$/', $file)) {
            $this->line('Error: Tên class không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$file);
            return self::ERROR;
        }

        $folder = \Theme::name();

        return $this->creatFile($folder, $file);
    }

    public function creatFile($folder, $file): bool
    {
        $path = $folder.'/core/Table/Columns/'.$file.'.php';

        $samplePath = 'plugins/DevTool/sample/core/column.php';

        if(file_exists('views/'.$path)) {
            $this->line('Error: file column views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
            return self::ERROR;
        }

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file column sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$samplePath);
            return self::ERROR;
        }

        $storage = \Storage::disk('views');

        if(!file_exists('views/'.$folder.'/core')) {
            mkdir('views/'.$folder.'/core', 0775);
        }

        if(!file_exists('views/'.$folder.'/core/Table')) {
            mkdir('views/'.$folder.'/core/Table', 0775);
        }

        if(!file_exists('views/'.$folder.'/core/Table/Columns')) {
            mkdir('views/'.$folder.'/core/Table/Columns', 0775);
        }

        $fileSample = $storage->get($samplePath);

        $fileSample = str_replace('COLUMN_CLASS_NAME', $file, $fileSample);

        if($storage->put($path, $fileSample)) {

            $this->line(function (Message $message) use ($file) {
                $message->line('success!', 'green');
                $message->line('Column '.$file.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file column thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }
}
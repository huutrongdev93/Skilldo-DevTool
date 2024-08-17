<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;

class CommandMakeTable extends Command {

    protected string $signature = 'make:table {table} {class}';

    protected string $description = 'Generates a table';

    public function handle(): bool
    {
        $table = $this->argument('table');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', $table)) {
            $this->line('Error: Tên file table không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ table name: '.$table);
            return self::ERROR;
        }

        $class = $this->argument('class');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', $class)) {
            $this->line('Error: Tên class table không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        if(class_exists($class)) {
            $this->line('Error: Class này đã được sử dụng');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        $folder = \Theme::name();

        return $this->creatFile($folder, $table, $class);
    }

    public function creatFile($folder, $file, $class): bool
    {
        $path = $folder.'/theme-custom/table/'.$file.'.table.php';

        if(file_exists('views/'.$path)) {
            $this->line('Error: file table views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
            return self::ERROR;
        }

        $samplePath = 'plugins/DevTool/sample/table.php';

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file table sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$samplePath);
            return self::ERROR;
        }

        $storage = \Storage::disk('views');

        if(!file_exists('views/'.$folder.'/theme-custom/table')) {
            mkdir('views/'.$folder.'/theme-custom/table', 0775);
        }

        $fileSample = $storage->get($samplePath);

        $fileSample = str_replace('TABLE_CLASS_NAME', $class, $fileSample);

        $fileSample = str_replace('TABLE_NAME', $file, $fileSample);

        if($storage->put($path, $fileSample)) {

            $this->line(function (Message $message) use ($file) {
                $message->line('success!', 'green');
                $message->line('table '.$file.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file table thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }
}
<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;

class CommandMakeLangTheme extends Command {

    protected string $signature = 'make:lang:theme {locale} {file}';

    protected string $description = 'Generates language translation';

    public function handle(): bool
    {
        $local = $this->argument('local');

        if(!preg_match('/^[a-zA-Z0-9]+$/', $local)) {
            $this->line('Error: locale không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ local: '.$local);
            return self::ERROR;
        }

        $file = $this->argument('file');

        if(!preg_match('/^[a-zA-Z0-9]+$/', $file)) {
            $this->line('Error: file không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ File: '.$file);
            return self::ERROR;
        }

        $storage = \Storage::disk('views');

        $folder = \Theme::name();

        $path = $folder.'/language/'.$local.'/'.$file.'.php';

        if($storage->has($path)) {
            $this->line('Error: file language '.$file.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
            return self::ERROR;
        }

        $samplePath = 'plugins/DevTool/sample/language.php';

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file language sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$samplePath);
            return self::ERROR;
        }

        if(!file_exists('views/'.$folder.'/language')) {
            mkdir('views/'.$folder.'/language', 0775);
        }

        if(!file_exists('views/'.$folder.'/language/'.$local)) {
            mkdir('views/'.$folder.'/language/'.$local, 0775);
        }

        $fileSample = $storage->get($samplePath);

        if($storage->put($path, $fileSample)) {

            $this->line(function (Message $message) use ($file) {
                $message->line('success!', 'green');
                $message->line('file translation '.$file.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file translation thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }
}
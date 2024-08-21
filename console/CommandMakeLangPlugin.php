<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;

class CommandMakeLangPlugin extends Command {

    protected string $signature = 'make:lang:plugin {plugin} {file}';

    protected string $description = 'Generates language translation';

    public function handle(): bool
    {
        $plugin = $this->argument('plugin');

        if(!preg_match('/^[a-zA-Z0-9-]+$/', $plugin)) {
            $this->line('Error: tên thư mục plugin không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ plugin: '.$plugin);
            return self::ERROR;
        }

        $file = $this->argument('file');

        if(!preg_match('/^[a-zA-Z0-9]+$/', $file)) {
            $this->line('Error: file không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ File: '.$file);
            return self::ERROR;
        }

        $storage = \Storage::disk('plugin');

        $samplePath = 'DevTool/sample/language.php';

        if(!$storage->has($samplePath)) {
            $this->line('Error: file language sample not found.');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        $folder = $plugin;

        $languages = \Language::listKey();

        $fileSample = $storage->get($samplePath);

        foreach ($languages as $local) {

            $pathFolder = $folder.'/language/'.$local;

            $path = $pathFolder.'/'.$file.'.php';

            if($storage->has($path)) {
                $this->line('Error: file language '.$file.' is exits.');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ views/'.$path);
                return self::ERROR;
            }
            if(!$storage->has($pathFolder) && !$storage->makeDirectory($pathFolder)) {
                $this->line('Error: folder language '.$pathFolder.' cannot be created');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ views/'.$pathFolder);
                return self::ERROR;
            }
            try {
                if(!$storage->put($path, $fileSample)) {
                    $this->line('Error: file language '.$file.' cannot be created.');
                    $this->line('+ '.$this->fullCommand());
                    $this->line('+ views/'.$path);
                    return self::ERROR;
                }
            }
            catch (\Exception $e) {
                $this->line($e->getMessage());
                $this->line('+ '.$this->fullCommand());
                $this->line('+ views/'.$path);
                return self::ERROR;
            }
        }

        $this->line(function (Message $message) use ($file) {
            $message->line('success!', 'green');
            $message->line('file translation '.$file.' is created');
        });

        return self::SUCCESS;
    }
}
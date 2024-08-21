<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;

class CommandMakeLangTheme extends Command {

    protected string $signature = 'make:lang:theme {file}';

    protected string $description = 'Generates language translation';

    public function handle(): bool
    {
        $file = $this->argument('file');

        if(!preg_match('/^[a-zA-Z0-9]+$/', $file)) {
            $this->line('Error: file không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ File: '.$file);
            return self::ERROR;
        }

        $samplePath = 'plugins/DevTool/sample/language.php';

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file language sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$samplePath);
            return self::ERROR;
        }

        $storage = \Storage::disk('views');

        $folder = \Theme::name();

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

            if(!$storage->put($path, $fileSample)) {
                $this->line('Error: file language '.$file.' cannot be created.');
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
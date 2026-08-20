<?php
namespace DevTool\Console;
use DevTool\Commands\Command;
use DevTool\Commands\Message;
use Illuminate\Support\Facades\Storage;
use SkillDo\Cms\Support\Language;
use SkillDo\Cms\Support\Theme;
use SkillDo\Support\Path;

class CommandMakeLangTheme extends Command {

    protected string $signature = 'make:lang:theme {file}';

    protected string $description = 'Generates language translation';

    public function handle(): bool
    {
        $file = $this->argument('file');

        if(!preg_match('/^[a-zA-Z0-9]+$/', $file))
        {
            $this->line('Error: file không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ File: '.$file);
            return self::ERROR;
        }

        $samplePath = Path::plugin('DevTool/sample/language.php');

        if(!file_exists($samplePath)) {
            $this->line('Error: file language sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        $storage = Storage::disk('views');

        $folder = Theme::name();

        $languages = Language::listKey();

        $fileSample = file_get_contents($samplePath);

        foreach ($languages as $local) {

            $pathFolder = $folder.'/language/'.$local;

            $path = $pathFolder.'/'.$file.'.php';

            if($storage->has($path))
            {
                $this->line('Error: file language '.$file.' is exits.');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ views/'.$path);
                return self::ERROR;
            }

            if(!$storage->has($pathFolder) && !$storage->makeDirectory($pathFolder))
            {
                $this->line('Error: folder language '.$pathFolder.' cannot be created');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ views/'.$pathFolder);
                return self::ERROR;
            }

            if(!$storage->put($path, $fileSample))
            {
                $this->line('Error: file language '.$file.' cannot be created.');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ views/'.$path);
                return self::ERROR;
            }
        }

        $this->line(function (Message $message) use ($file)
        {
            $message->line('success!', 'green');
            $message->line('file translation '.$file.' is created');
        });

        return self::SUCCESS;
    }
}

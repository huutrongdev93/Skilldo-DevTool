<?php
namespace SkillDo\DevTool\Console;
use JetBrains\PhpStorm\NoReturn;
use SkillDo\DevTool\Commands\Command;

class CommandThemeChildCopy extends Command
{
    protected string $signature = 'theme:child:copy {source}';

    protected string $description = 'Copy a copy from the main theme into the child theme';

    #[NoReturn]
    public function handle(): bool
    {
        $themeName = \Theme::name();

        $pathTheme = 'views/'.$themeName.'/';

        $pathSource = $this->argument('source');

        if(!file_exists($pathTheme.$pathSource)) {
            $this->line('file or dir '.$pathTheme.$pathSource.' not exists');
            return self::ERROR;
        }

        if(file_exists($pathTheme.'theme-child/'.$pathSource)) {
            $this->line('file or dir '.$pathSource.' is existing in theme-child');
            return self::ERROR;
        }

        $isFolder =  true;

        if(is_file($pathTheme.$pathSource)) {
            $isFolder = false;
        }

        $storage = \Storage::disk('views');

        if(!$isFolder) {

            $storage->copy($themeName.'/'.$pathSource, $themeName.'/'.'theme-child/'.$pathSource);

            $this->line('copy file success', 'green');

            $this->line('+ from '.$pathTheme.$pathSource);

            $this->line('+ to '.$pathTheme.'theme-child/'.$pathSource);

            return self::SUCCESS;
        }

        $allFiles = $storage->allFiles($themeName.'/'.$pathSource);

        foreach ($allFiles as $pathFrom) {

            $pathTo = str_replace($themeName.'/', $themeName.'/theme-child/', $pathFrom);

            $storage->copy($pathFrom, $pathTo);
        }

        $this->line('copy folder success', 'green');

        $this->line('+ from '.$pathTheme.$pathSource);

        $this->line('+ to '.$pathTheme.'theme-child/'.$pathSource);

        return self::SUCCESS;
    }
}
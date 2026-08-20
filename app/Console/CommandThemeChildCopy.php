<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use Illuminate\Support\Facades\Storage;
use SkillDo\Cms\Support\Theme;

class CommandThemeChildCopy extends Command
{
    protected string $signature = 'theme:child:copy {source}';

    protected string $description = 'Copy a file or folder from the main theme into the child theme';

    public function handle(): bool
    {
        $storage = Storage::disk('views');

        /*
         * v8: theme con là thư mục NGANG HÀNG với theme cha (views/theme-child),
         * không còn lồng trong views/<theme>/theme-child như v7.
         */
        $theme = Theme::name();

        $themeChild = config('cms.theme.child');

        if($theme === $themeChild)
        {
            $this->line('Error: theme hiện tại đang chính là theme con');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        $source = trim(str_replace('\\', '/', (string) $this->argument('source')), '/');

        if($source === '' || str_contains($source, '..'))
        {
            $this->line('Error: đường dẫn nguồn không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ source: '.$source);
            return self::ERROR;
        }

        $from = $theme.'/'.$source;

        $to = $themeChild.'/'.$source;

        $isFile = $storage->fileExists($from);

        if(!$isFile && !$storage->directoryExists($from))
        {
            $this->line('Error: file hoặc thư mục views/'.$from.' không tồn tại');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        if($storage->fileExists($to) || $storage->directoryExists($to))
        {
            $this->line('Error: views/'.$to.' đã tồn tại trong theme con');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        if($isFile)
        {
            $storage->copy($from, $to);

            $this->line(function (Message $message) use ($from, $to) {
                $message->line('copy file success', 'green');
                $message->line('+ from views/'.$from);
                $message->line('+ to views/'.$to);
            });

            return self::SUCCESS;
        }

        $files = $storage->allFiles($from);

        if(empty($files))
        {
            $this->line('Error: thư mục views/'.$from.' rỗng');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        foreach ($files as $pathFrom)
        {
            //chỉ thay tiền tố thư mục theme, giữ nguyên phần còn lại của đường dẫn
            $pathTo = $themeChild.'/'.substr($pathFrom, strlen($theme) + 1);

            $storage->copy($pathFrom, $pathTo);
        }

        $this->line(function (Message $message) use ($from, $to, $files) {
            $message->line('copy folder success', 'green');
            $message->line('+ '.count($files).' file');
            $message->line('+ from views/'.$from);
            $message->line('+ to views/'.$to);
        });

        return self::SUCCESS;
    }
}

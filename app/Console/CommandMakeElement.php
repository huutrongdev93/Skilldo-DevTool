<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SkillDo\Support\Path;

class CommandMakeElement extends Command {

    protected string $signature = 'make:element {folder} {class?} {file?} {--type=general}';

    protected string $description = 'Generates element class (register in elements/elements.json)';

    const TYPES = ['general', 'header', 'footer'];

    public function handle(): bool
    {
        $storage = Storage::disk('views');

        $type = $this->option('type');

        $type = (is_string($type) && $type !== '') ? Str::lower($type) : 'general';

        if(!in_array($type, self::TYPES))
        {
            $this->line('Error: --type phải là một trong: '.implode(', ', self::TYPES));
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        $folder = $this->argument('folder');

        if(!preg_match('/^[a-zA-Z0-9-]+$/', (string) $folder))
        {
            $this->line('Error: Tên folder không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ folder name: '.$folder);
            return self::ERROR;
        }

        /*
         * Element hỗ trợ theme-child (ElementManager đọc cả theme và theme-child),
         * nên element mới luôn sinh vào theme-child để không bị mất khi update theme.
         */
        $theme = config('cms.theme.child');

        $folderPath = $theme.'/elements/'.$folder;

        if(!$storage->has($folderPath))
        {
            if(!$storage->makeDirectory($folderPath))
            {
                $this->line('Error: Không tạo được thư mục chứa element');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ folder path: '.$folderPath);
                return self::ERROR;
            }
        }

        //Lấy số style tiếp theo
        $maxStyleNumber = 0;

        foreach ($storage->directories($folderPath) as $path)
        {
            if (preg_match('/style(\d+)$/', $path, $matches))
            {
                $styleNumber = (int)$matches[1];

                if ($styleNumber > $maxStyleNumber)
                {
                    $maxStyleNumber = $styleNumber;
                }
            }
        }

        $maxStyleNumber += 1;

        //Lấy tên class element
        $class = $this->argument('class');

        if(empty($class))
        {
            $class = Str::studly($folder).'ElementStyle'.$maxStyleNumber;
        }

        if(!preg_match('/^[a-zA-Z0-9_]+$/', (string) $class))
        {
            $this->line('Error: Tên class không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        if(class_exists($class))
        {
            $this->line('Error: Tên class này đã được sử dụng');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        $samplePath = Path::plugin('DevTool/sample/widget/element.php');

        if(!file_exists($samplePath))
        {
            $this->line('Error: file element sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        //Lấy tên file element
        $file = $this->argument('file');

        if(empty($file))
        {
            $file = $folder.'-style'.$maxStyleNumber;
        }

        if(!preg_match('/^[a-zA-Z0-9_-]+$/', (string) $file))
        {
            $this->line('Error: Tên file không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ file name: '.$file);
            return self::ERROR;
        }

        $style = 'style'.$maxStyleNumber;

        $pathNew = $folderPath.'/'.$style;

        if(!$storage->makeDirectory($pathNew))
        {
            $this->line('Error: không tạo được thư mục style');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ path: '.$pathNew);
            return self::ERROR;
        }

        $assetName = $folder.'-style'.$maxStyleNumber;

        $files = [
            $pathNew.'/assets/'.$assetName.'.less' => '',
            $pathNew.'/assets/'.$assetName.'.js'   => '',
            $pathNew.'/views/view.blade.php'       => '',
        ];

        foreach ($files as $path => $content)
        {
            if(!$storage->put($path, $content))
            {
                $this->line('Error: không tạo được file '.$path);
                $this->line('+ '.$this->fullCommand());
                $storage->deleteDirectory($pathNew);
                return self::ERROR;
            }
        }

        $fileSample = Str::of(file_get_contents($samplePath))
            ->replace('ELEMENT_CLASS_NAME', $class)
            ->replace('ELEMENT_LABEL', Str::headline($folder))
            ->replace('ELEMENT_STYLE_NUMBER', (string) $maxStyleNumber)
            ->replace('ELEMENT_FILE_STYLE', $assetName)
            ->replace('ELEMENT_FILE_JS', $assetName)
            ->replace('ELEMENT_TAG', $folder)
            ->toString();

        $classFile = $pathNew.'/'.$file.'.widget.php';

        if(!$storage->put($classFile, $fileSample))
        {
            $this->line('Error: Tạo file element thất bại kiểm tra lại quyền đọc ghi thư mục');
            $storage->deleteDirectory($pathNew);
            return self::ERROR;
        }

        /*
         * Bắt buộc: đăng ký vào elements.json, lõi không quét thư mục
         */
        $registered = DevtoolHelper::registerJsonWidget(
            Path::themeChild('elements/elements.json'),
            $type,
            $class,
            'elements/'.$folder.'/'.$style.'/'.$file.'.widget.php'
        );

        if(!$registered)
        {
            $this->line('Error: không ghi được vào '.$theme.'/elements/elements.json');
            $this->line('+ '.$this->fullCommand());
            $storage->deleteDirectory($pathNew);
            return self::ERROR;
        }

        $this->line(function (Message $message) use ($class, $type)
        {
            $message->line('success!', 'green');
            $message->line('element');
            $message->line($class, 'blue');
            $message->line('is created and registered in elements.json ('.$type.')');
        });

        return self::SUCCESS;
    }
}

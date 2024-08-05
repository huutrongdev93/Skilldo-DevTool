<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;
use Str;
use Theme;

class CommandMakeWidgetSidebar extends Command {

    protected string $signature = 'make:widget:sidebar {type} {folder} {class?} {file?}';

    protected string $description = 'Generates widget sidebar class';

    public function handle(): bool
    {
        $storage = \Storage::disk('views');

        $type = $this->argument('type');

        if(empty($type)) {
            $this->line('Error: Bạn chưa chọn loại sidebar để tạo widget');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        if(!in_array($type, ['sidebar', 'detail', 'list'])) {
            $this->line('Error: Loại sidebar không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ type name: '.$type);
            return self::ERROR;
        }

        $folder = $this->argument('folder');

        if(!preg_match('/^[a-zA-Z0-9-]+$/', $folder)) {
            $this->line('Error: Tên folder không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ folder name: '.$folder);
            return self::ERROR;
        }

        $sidebarFolder = 'common';

        if($type == 'detail') {
            $sidebarFolder = 'detail';
        }
        if($type == 'list') {
            $sidebarFolder = 'list';
        }

        $folderPath = Theme::name().'/widget/sidebar/'.$sidebarFolder.'/'.$folder;

        if(!$storage->has($folderPath)) {

            if(!$storage->makeDirectory($folderPath)) {
                $this->line('Error: Không tạo được thư mục chưa widget');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ folder path: '.$folderPath);
                return self::ERROR;
            }
        }

        //Lấy số style tiếp theo
        $directories = $storage->directories($folderPath);

        $maxStyleNumber = 0;

        if(!empty($directories)) {
            foreach ($directories as $path) {
                // Lấy phần số phía sau 'style'
                if (preg_match('/style(\d+)$/', $path, $matches)) {
                    $styleNumber = (int)$matches[1];
                    if ($styleNumber > $maxStyleNumber) {
                        $maxStyleNumber = $styleNumber;
                    }
                }
            }
        }

        $maxStyleNumber +=1;

        //Lấy tên class widget
        $class = $this->argument('class');

        if(empty($class)) {
            $class = 'widget_sidebar_'.str_replace('-', '_', $folder).'_style_'.$maxStyleNumber;
        }

        if(!preg_match('/^[a-zA-Z0-9_-]+$/', $class)) {
            $this->line('Error: Tên class không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        if(class_exists($class)) {
            $this->line('Error: Tên class này đã được sử dụng');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        $class = explode('_', $class);

        $class = array_map('ucfirst', $class);

        $class = implode('_', $class);

        if(class_exists($class)) {
            $this->line('Error: Tên class này đã được sử dụng');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        $samplePath = 'plugins/DevTool/sample/widget/widget-sidebar.php';

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file widget sample not found.');
            $this->line($this->fullCommand());
            $this->line('+ views/'.$samplePath);
            return self::ERROR;
        }

        //Lấy tên file widget
        $file = $this->argument('file');

        if(empty($file)) {
            $file = $folder.'_style_'.$maxStyleNumber;
        }

        if(!preg_match('/^[a-zA-Z0-9_-]+$/', $file)) {
            $this->line('Error: Tên file không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ file name: '.$file);
            return self::ERROR;
        }

        $style = 'style'.$maxStyleNumber;

        $pathNew = $folderPath.'/'.$style;

        if(!$storage->makeDirectory($pathNew)) {
            $this->line('Error: không tạo được thư mục style');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ path: '.$pathNew);
            return self::ERROR;
        }

        $pathAssets = $folderPath.'/'.$style.'/assets';

        if(!$storage->makeDirectory($pathAssets)) {
            $this->line('Error: không tạo được thư mục assets');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ path: '.$pathAssets);
            $storage->deleteDirectory($pathNew);
            return self::ERROR;
        }

        $pathAssetsStyle = $pathAssets.'/'.$folder.'-style-'.$maxStyleNumber.'.less';

        if(!$storage->put($pathAssetsStyle, '')) {
            $this->line('Error: không tạo được file css');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ path: '.$pathAssetsStyle);
            $storage->deleteDirectory($pathNew);
            return self::ERROR;
        }

        $pathAssetsJs = $pathAssets.'/'.$folder.'-style-'.$maxStyleNumber.'.js';

        if(!$storage->put($pathAssetsJs, '')) {
            $this->line('Error: không tạo được file js');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ path: '.$pathAssetsJs);
            $storage->deleteDirectory($pathNew);
            return self::ERROR;
        }

        $pathViews = $folderPath.'/'.$style.'/views';

        if(!$storage->makeDirectory($pathViews)) {
            $this->line('Error: không tạo được thư mục views');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ path: '.$pathViews);
            $storage->deleteDirectory($pathNew);
            return self::ERROR;
        }

        $pathViewsFile = $pathViews.'/view.blade.php';

        if(!$storage->put($pathViewsFile, '')) {
            $this->line('Error: không tạo được file view');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ path: '.$pathViewsFile);
            $storage->deleteDirectory($pathNew);
            return self::ERROR;
        }

        $fileSample = $storage->get($samplePath);

        $fileSample = str_replace('WIDGET_CLASS_NAME', $class, $fileSample);
        $fileSample = str_replace('WIDGET_CLASS', Str::lower($class), $fileSample);
        $fileSample = str_replace('WIDGET_STYLE_NUMBER', $maxStyleNumber, $fileSample);
        $fileSample = str_replace('WIDGET_FILE_STYLE', $folder.'-style-'.$maxStyleNumber, $fileSample);
        $fileSample = str_replace('WIDGET_FILE_JS', $folder.'-style-'.$maxStyleNumber, $fileSample);
        $fileSample = str_replace('WIDGET_TAG', $folder, $fileSample);
        if($type == 'sidebar' || $type == 'list') {
            $fileSample .= "\n";
            $fileSample .= "WidgetSidebar::add('".$class."', 'list');";
        }
        if($type == 'sidebar' || $type == 'detail') {
            $fileSample .= "\n";
            $fileSample .= "WidgetSidebar::add('".$class."', 'detail');";
        }

        if($storage->put($pathNew.'/'.$file.'.widget.php', $fileSample)) {

            $this->line(function (Message $message) use ($file) {
                $message->line('success!', 'green');
                $message->line('widget '.$file.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file widget thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }
}
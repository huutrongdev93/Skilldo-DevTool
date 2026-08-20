<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Support\Path;

class CommandMakePopoverTheme extends Command {

    protected string $signature = 'make:popover:theme {class}';

    protected string $description = 'Generates form popover class (register in theme-child.json)';

    public function handle(): bool
    {
        $class = $this->argument('class');

        if(!preg_match('/^[a-zA-Z0-9_-]+$/', (string) $class))
        {
            $this->line('Error: Tên class không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        $class = Str::studly($class);

        if(!Str::endsWith($class, 'Popover'))
        {
            $class .= 'Popover';
        }

        //module key dùng trong form: ['search' => '{module}']
        $module = Str::kebab(Str::replaceLast('Popover', '', $class));

        $storage = Storage::disk('views');

        $path = config('cms.theme.child').'/app/Cms/Form/Popovers/'.$class.'.php';

        if($storage->has($path))
        {
            $this->line('Error: file popover views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
            return self::ERROR;
        }

        $samplePath = Path::plugin('DevTool/sample/core/form-popover.php');

        if(!file_exists($samplePath))
        {
            $this->line('Error: file popover sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        $namespace = 'Theme\Cms\Form\Popovers';

        $fileSample = Str::of(file_get_contents($samplePath))
            ->replace('POPOVER_NAMESPACE', $namespace)
            ->replace('POPOVER_CLASS_NAME', $class)
            ->replace('POPOVER_MODULE', $module)
            ->toString();

        if(!$storage->put($path, $fileSample))
        {
            $this->line('Error: Tạo file popover thất bại kiểm tra lại quyền đọc ghi thư mục');
            return self::ERROR;
        }

        /*
         * Bắt buộc: lõi chỉ nhận popover khai báo ở cms.form.popover
         */
        $registered = DevtoolHelper::registerManifestCmsForm(
            Path::themeChild('theme-child.json'),
            'popover',
            $module,
            $namespace.'\\'.$class,
            createIfMissing: true
        );

        if(!$registered)
        {
            $this->line('Error: không ghi được vào '.config('cms.theme.child').'/theme-child.json');
            $this->line('+ '.$this->fullCommand());
            $storage->delete($path);
            return self::ERROR;
        }

        $this->line(function (Message $message) use ($module) {
            $message->line('success!', 'green');
            $message->line('popover');
            $message->line($module, 'blue');
            $message->line('is created and registered in theme-child.json');
        });

        return self::SUCCESS;
    }
}

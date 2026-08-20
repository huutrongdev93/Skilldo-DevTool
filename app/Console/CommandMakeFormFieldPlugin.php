<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Cms\Plugin\Plugin;
use SkillDo\Support\Path;

class CommandMakeFormFieldPlugin extends Command {

    protected string $signature = 'make:form-field:plugin {plugin} {class}';

    protected string $description = 'Generates form field class (register in plugin.json)';

    public function handle(): bool
    {
        $pluginName = $this->argument('plugin');

        if(!preg_match('/^[a-zA-Z0-9._-]+$/', (string) $pluginName))
        {
            $this->line('Error: tên thư mục plugin không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ plugin: '.$pluginName);
            return self::ERROR;
        }

        $plugin = Plugin::get($pluginName);

        if(empty($plugin))
        {
            $this->line('Error: plugin not found.');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        $class = $this->argument('class');

        if(!preg_match('/^[a-zA-Z0-9_]+$/', (string) $class))
        {
            $this->line('Error: Tên class không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        $class = Str::studly($class);

        //tên field dùng trong form: $form->{type}(...)
        $type = Str::camel($class);

        $storage = Storage::disk('plugins');

        $path = $plugin->getId().'/app/Cms/Form/Field/'.$class.'.php';

        if($storage->has($path))
        {
            $this->line('Error: file form field '.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$path);
            return self::ERROR;
        }

        $samplePath = Path::plugin('DevTool/sample/core/form-field.php');

        if(!file_exists($samplePath))
        {
            $this->line('Error: file form field sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        $namespace = DevtoolHelper::pluginAlias($plugin).'\Cms\Form\Field';

        $fileSample = Str::of(file_get_contents($samplePath))
            ->replace('FORM_FIELD_NAMESPACE', $namespace)
            ->replace('FORM_FIELD_CLASS_NAME', $class)
            ->replace('FORM_FIELD_TYPE', $type)
            ->toString();

        if(!$storage->put($path, $fileSample))
        {
            $this->line('Error: Tạo file form field thất bại kiểm tra lại quyền đọc ghi thư mục');
            return self::ERROR;
        }

        /*
         * Bắt buộc: lõi chỉ nhận field khai báo ở cms.form.fields trong plugin.json
         */
        $registered = DevtoolHelper::registerManifestCmsForm(
            Path::plugin($plugin->getId().'/plugin.json'),
            'fields',
            $type,
            ['class' => $namespace.'\\'.$class]
        );

        if(!$registered)
        {
            $this->line('Error: không ghi được vào '.$plugin->getId().'/plugin.json');
            $this->line('+ '.$this->fullCommand());
            $storage->delete($path);
            return self::ERROR;
        }

        $this->line(function (Message $message) use ($type) {
            $message->line('success!', 'green');
            $message->line('form field');
            $message->line('$form->'.$type.'()', 'blue');
            $message->line('is created and registered in plugin.json');
        });

        return self::SUCCESS;
    }
}

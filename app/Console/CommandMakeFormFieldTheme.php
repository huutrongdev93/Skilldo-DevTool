<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Support\Path;

class CommandMakeFormFieldTheme extends Command {

    protected string $signature = 'make:form-field:theme {class}';

    protected string $description = 'Generates form field class (register in theme-child.json)';

    public function handle(): bool
    {
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

        $storage = Storage::disk('views');

        $path = config('cms.theme.child').'/app/Cms/Form/Field/'.$class.'.php';

        if($storage->has($path))
        {
            $this->line('Error: file form field views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
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

        $namespace = 'Theme\Cms\Form\Field';

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
         * Bắt buộc: lõi chỉ nhận field khai báo ở cms.form.fields.
         * Ghi vào theme-child.json (file bổ sung của theme con, không override theme cha)
         */
        $registered = DevtoolHelper::registerManifestCmsForm(
            Path::themeChild('theme-child.json'),
            'fields',
            $type,
            ['class' => $namespace.'\\'.$class],
            createIfMissing: true
        );

        if(!$registered)
        {
            $this->line('Error: không ghi được vào '.config('cms.theme.child').'/theme-child.json');
            $this->line('+ '.$this->fullCommand());
            $storage->delete($path);
            return self::ERROR;
        }

        $this->line(function (Message $message) use ($type) {
            $message->line('success!', 'green');
            $message->line('form field');
            $message->line('$form->'.$type.'()', 'blue');
            $message->line('is created and registered in theme-child.json');
        });

        return self::SUCCESS;
    }
}

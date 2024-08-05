<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;

class CommandMakeFormFieldTheme extends Command {

    protected string $signature = 'make:form-field:theme {class}';

    protected string $description = 'Generates form field class';

    public function handle(): bool
    {
        $file = $this->argument('class');

        if(!preg_match('/^[a-zA-Z0-9]+$/', $file)) {
            $this->line('Error: Tên file không hợp lệ');
            $this->line($this->fullCommand());
            $this->line('file name: '.$file);
            return self::ERROR;
        }

        $folder = \Theme::name();

        return $this->creatFormFile($folder, $file);
    }

    public function creatFormFile($folder, $file): bool
    {
        $path = $folder.'/core/Form/Field/'.$file.'.php';

        $samplePath = 'plugins/DevTool/sample/core/form-field.php';

        if(file_exists('views/'.$path)) {
            $this->line('Error: file form field views/'.$path.' is exits.');
            $this->line($this->fullCommand());
            $this->line('views/'.$path);
            return self::ERROR;
        }

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file form field sample not found.');
            $this->line($this->fullCommand());
            $this->line('views/'.$samplePath);
            return self::ERROR;
        }

        $storage = \Storage::disk('views');

        if(!file_exists('views/'.$folder.'/core')) {
            mkdir('views/'.$folder.'/core', 0775);
        }

        if(!file_exists('views/'.$folder.'/core/Form')) {
            mkdir('views/'.$folder.'/core/Form', 0775);
        }

        if(!file_exists('views/'.$folder.'/core/Form/Field')) {
            mkdir('views/'.$folder.'/core/Form/Field', 0775);
        }

        $fileSample = $storage->get($samplePath);

        $fileSample = str_replace('FormFieldClassName', $file, $fileSample);

        if($storage->put($path, $fileSample)) {

            $this->line(function (Message $message) use ($file) {
                $message->line('success!', 'green');
                $message->line('form field '.$file.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file form field thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }
}
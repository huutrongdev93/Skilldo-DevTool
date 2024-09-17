<?php
namespace SkillDo\DevTool\Console;
use Cms;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;

class CommandMakeMiddleware extends Command {

    protected string $signature = 'make:middleware {class}';

    protected string $description = 'Generates an Middleware';

    public function handle(): bool
    {
        $class = $this->argument('class');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', $class)) {
            $this->line('Error: Tên class Middleware không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class name: '.$class);
            return self::ERROR;
        }

        return $this->creatFile($class);
    }

    public function creatFile($class): bool
    {
        $class = \Str::ucfirst($class);

        $path = 'middleware/'.$class.'.php';

        if(file_exists($path)) {
            $this->line('Error: file middleware '.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ middleware/'.$path);
            return self::ERROR;
        }

        $samplePath = 'plugins/DevTool/sample/middleware.php';

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file middleware sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$samplePath);
            return self::ERROR;
        }

        $storage = \Storage::disk('root');

        $fileSample = $storage->get('views/'.$samplePath);

        $fileSample = str_replace('CLASS_NAME', $class, $fileSample);

        if($storage->put($path, $fileSample)) {

            $this->line(function (Message $message) use ($class) {
                $message->line('success!', 'green');
                $message->line('middleware '.$class.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file middleware thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }
}
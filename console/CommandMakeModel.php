<?php
namespace SkillDo\DevTool\Console;
use Cms;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;

class CommandMakeModel extends Command {

    protected string $signature = 'make:model {table} {--db}';

    protected string $description = 'Generates an model';

    public function handle(): bool
    {
        $table = $this->argument('table');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', $table)) {
            $this->line('Error: Tên file table không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ table name: '.$table);
            return self::ERROR;
        }

        $db = $this->option('db');

        $folder = \Theme::name();

        return $this->creatFile($folder, $table, $db);
    }

    public function creatFile($folder, $file, $db): bool
    {
        $name = \Str::ucfirst($file);

        $path = $folder.'/theme-custom/model/'.$file.'.model.php';

        if(file_exists('views/'.$path)) {
            $this->line('Error: file model views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
            return self::ERROR;
        }

        if(version_compare(Cms::version(), '7.1.0', '<')) {
            $samplePath = 'plugins/DevTool/sample/model.php';
        }
        else {
            $samplePath = 'plugins/DevTool/sample/model-7.1.x.php';
        }

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file model sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$samplePath);
            return self::ERROR;
        }

        $storage = \Storage::disk('views');

        if(!file_exists('views/'.$folder.'/theme-custom/model')) {
            mkdir('views/'.$folder.'/theme-custom/model', 0775);
        }

        //Kiểm tra nếu có tùy chọn tạo file db
        if(!is_null($db)) {

            $dbName = $file.'-'.date('d-m-Y');

            $dbPath = $folder.'/database/'.$dbName.'.php';

            if(file_exists('views/'.$dbPath)) {
                $this->line('Error: file database views/'.$dbPath.' is exits.');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ views/'.$dbPath);
                return self::ERROR;
            }

            if(!file_exists('views/'.$folder.'/database')) {
                mkdir('views/'.$folder.'/database', 0775);
            }

            $dbContent = $storage->get('plugins/DevTool/sample/database-model.php');

            $dbContent = str_replace('MODEL_TABLE_NAME', $file, $dbContent);
        }

        $fileSample = $storage->get($samplePath);

        $fileSample = str_replace('MODEL_NAME', $name, $fileSample);

        $fileSample = str_replace('MODEL_TABLE', $file, $fileSample);

        if($storage->put($path, $fileSample)) {

            if(!is_null($db) && !empty($dbContent) && !empty($dbPath)) {
                $storage->put($dbPath, $dbContent);
            }

            $this->line(function (Message $message) use ($file) {
                $message->line('success!', 'green');
                $message->line('model '.$file.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file model thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }
}
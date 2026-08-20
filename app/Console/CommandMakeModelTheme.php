<?php
namespace DevTool\Console;
use DevTool\Commands\Command;
use DevTool\Commands\Message;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Support\Path;

class CommandMakeModelTheme extends Command {

    protected string $signature = 'make:model:theme {table} {--db}';

    protected string $description = 'Generates an model';

    public function handle(): bool
    {
        $table = $this->argument('table');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', $table))
        {
            $this->line('Error: Tên file table không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ table name: '.$table);
            return self::ERROR;
        }

        $db = $this->option('db');

        $folder = config('cms.theme.child');

        return $this->creatFile($folder, $table, $db);
    }

    public function creatFile($folder, $table, $db): bool
    {
        $storage = Storage::disk('views');

        $className = Str::studly($table);

        $path = $folder.'/app/Models/'.$className.'.php';

        if($storage->has($path))
        {
            $this->line('Error: file model views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
            return self::ERROR;
        }

        $samplePath = Path::plugin('DevTool/sample/module/model.php');

        if(!file_exists($samplePath))
        {
            $this->line('Error: file model sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ '.$samplePath);
            return self::ERROR;
        }

        if(!is_null($db) && $db !== false)
        {
            $dbName = 'db-table-'.$table;

            $dbPath = $folder.'/database/'.$dbName.'.php';

            if($storage->has($dbPath))
            {
                $this->line('Error: file database views/'.$dbPath.' is exits.');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ views/'.$dbPath);
                return self::ERROR;
            }

            $dbSamplePath = Path::plugin('DevTool/sample/database.php');

            if(!file_exists($dbSamplePath))
            {
                $this->line('Error: file database sample not found.');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ '.$dbSamplePath);
                return self::ERROR;
            }

            $dbContent = file_get_contents($dbSamplePath);

            $dbContent = str_replace('MODEL_TABLE_NAME', $table, $dbContent);
        }

        $fileSample = file_get_contents($samplePath);

        $fileSample = Str::of($fileSample)
            ->replace('NAMESPACE_CLASS_NAME', 'Theme')
            ->replace('MODULE_CLASS_NAME', $className)
            ->replace('MODULE_MODEL_TABLE', $table)
            ->toString();;

        if($storage->put($path, $fileSample))
        {
            if(!is_null($db) && !empty($dbContent) && !empty($dbPath))
            {
                $storage->put($dbPath, $dbContent);
            }

            $this->line(function (Message $message) use ($table)
            {
                $message->line('success!', 'green');
                $message->line('model '.$table.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file model thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }
}

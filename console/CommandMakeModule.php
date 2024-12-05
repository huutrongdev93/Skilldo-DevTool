<?php
namespace SkillDo\DevTool\Console;
use Cms;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;
use Str;

class CommandMakeModule extends Command {

    protected string $signature = 'make:module {module} {model} {table}';

    protected string $description = 'Generates an module';

    public function handle(): bool
    {
        $module = $this->argument('module');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', $module)) {
            $this->line('Error: Tên module không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ module name: '.$module);
            return self::ERROR;
        }

        $model = $this->argument('model');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', $model)) {
            $this->line('Error: Tên class model không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ class model name: '.$model);
            return self::ERROR;
        }

        $table = $this->argument('table');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', $table)) {
            $this->line('Error: Tên table không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ table name: '.$table);
            return self::ERROR;
        }

        $folder = \Theme::name();

        return $this->creatFile($folder, $module, $model, $table);
    }

    public function creatFile($folder, $module, $model, $table): bool
    {
        $storage = \Storage::disk('views');

        $folderModule = Str::lower($module);

        $folderModule = str_replace('_', '-', $folderModule);

        //Check module file
        $pathModule = $folder.'/theme-custom/modules/'.$folderModule.'/'.$folderModule.'.module.php';
        if($storage->has($pathModule)) {
            $this->line('Error: file module views/'.$pathModule.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$pathModule);
            return self::ERROR;
        }

        //Check table file
        $pathTable = $folder.'/theme-custom/modules/'.$folderModule.'/'.$folderModule.'.table.php';
        if($storage->has($pathTable)) {
            $this->line('Error: file table views/'.$pathTable.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$pathTable);
            return self::ERROR;
        }

        //Check button file
        $pathButton = $folder.'/theme-custom/modules/'.$folderModule.'/'.$folderModule.'.button.php';
        if($storage->has($pathButton)) {
            $this->line('Error: file button views/'.$pathButton.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$pathButton);
            return self::ERROR;
        }

        //Check model file
        $pathModel = $folder.'/theme-custom/modules/'.$folderModule.'/'.$folderModule.'.model.php';
        if($storage->has($pathModel)) {
            $this->line('Error: file model views/'.$pathModel.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$pathModel);
            return self::ERROR;
        }

        $storage = \Storage::disk('views');

        if(!$storage->has($folder.'/theme-custom/modules') && !$storage->makeDirectory($folder.'/theme-custom/modules', 0775)) {
            $this->line('Error: không tạo được thư mục modules');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        if(!$storage->has($folder.'/theme-custom/modules/'.$folderModule) && !$storage->makeDirectory($folder.'/theme-custom/modules/'.$folderModule, 0775)) {
            $this->line('Error: không tạo được thư mục modules/'.$folderModule);
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        //validate
        $module_class_name = Str::ucWord($module, '_');

        $module_class_name = str_replace('_','', $module_class_name);

        //Module
        $moduleContent = $storage->get('plugins/DevTool/sample/module/module.php');

        $moduleContent = str_replace('MODULE_CLASS_NAME', $module_class_name, $moduleContent);

        $moduleContent = str_replace('MODULE_FOLDER', $folderModule, $moduleContent);

        $moduleContent = str_replace('MODULE_KEY', $module, $moduleContent);

        $moduleContent = str_replace('MODULE_MODEL_NAME', $model, $moduleContent);

        $moduleContent = str_replace('MODULE_MODEL_TABLE', $table, $moduleContent);

        //Table
        $tableContent = $storage->get('plugins/DevTool/sample/module/table.php');

        $tableContent = str_replace('MODULE_CLASS_NAME', $module_class_name, $tableContent);

        $tableContent = str_replace('MODULE_KEY', $module, $tableContent);

        $tableContent = str_replace('MODULE_FOLDER', $folderModule, $tableContent);

        $tableContent = str_replace('MODULE_MODEL_NAME', $model, $tableContent);

        $tableContent = str_replace('MODULE_MODEL_TABLE', $table, $tableContent);

        //Button
        $buttonContent = $storage->get('plugins/DevTool/sample/module/button.php');

        $buttonContent = str_replace('MODULE_CLASS_NAME', $module_class_name, $buttonContent);

        $buttonContent = str_replace('MODULE_KEY', $module, $buttonContent);

        $buttonContent = str_replace('MODULE_FOLDER', $folderModule, $buttonContent);

        $buttonContent = str_replace('MODULE_MODEL_NAME', $model, $buttonContent);

        $buttonContent = str_replace('MODULE_MODEL_TABLE', $table, $buttonContent);


        //Model
        $modelContent = $storage->get('plugins/DevTool/sample/module/model.php');

        $modelContent = str_replace('MODULE_MODEL_NAME', $model, $modelContent);

        $modelContent = str_replace('MODULE_MODEL_TABLE', $table, $modelContent);

        //Db
        $dbName = $table.'-'.date('d-m-Y');

        $pathDatabase = $folder.'/database/'.$dbName.'.php';

        if($storage->has($pathDatabase)) {
            $this->line('Error: file database views/'.$pathDatabase.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$pathDatabase);
            return self::ERROR;
        }

        if(!file_exists('views/'.$folder.'/database')) {
            mkdir('views/'.$folder.'/database', 0775);
        }

        $dbContent = $storage->get('plugins/DevTool/sample/database-model.php');

        $dbContent = str_replace('MODEL_TABLE_NAME', $table, $dbContent);

        //create file
        if(!$storage->put($pathModule, $moduleContent)) {
            $storage->deleteDirectory($folder.'/theme-custom/modules/'.$folderModule);
            $this->line('Error: file module views/'.$pathModule.' not make.');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        if(!$storage->put($pathTable, $tableContent)) {
            $storage->deleteDirectory($folder.'/theme-custom/modules/'.$folderModule);
            $this->line('Error: file table views/'.$pathTable.' not make.');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        if(!$storage->put($pathButton, $buttonContent)) {
            $storage->deleteDirectory($folder.'/theme-custom/modules/'.$folderModule);
            $this->line('Error: file button views/'.$pathButton.' not make.');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        if(!$storage->put($pathModel, $modelContent)) {
            $storage->deleteDirectory($folder.'/theme-custom/modules/'.$folderModule);
            $this->line('Error: file model views/'.$pathModel.' not make.');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        if(!$storage->put($pathDatabase, $dbContent)) {
            $storage->deleteDirectory($folder.'/theme-custom/modules/'.$folderModule);
            $this->line('Error: file database views/'.$pathDatabase.' not make.');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        $this->line(function (Message $message) use ($module) {
            $message->line('success!', 'green');
            $message->line('module '.$module.' is created');
        });

        return self::SUCCESS;
    }
}
<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use SkillDo\DevTool\Commands\Message;

class CommandMakeTaxonomy extends Command {

    protected string $signature = 'make:taxonomy {postType} {cateType?}';

    protected string $description = 'Generates taxonomy';

    public function handle(): bool
    {
        $postType = $this->argument('postType');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', $postType)) {
            $this->line('Error: Tên post type không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ post type name: '.$postType);
            return self::ERROR;
        }

        $cateType = $this->argument('cateType');

        if(!empty($cateType) && !preg_match('/^[a-zA-Z0-9-_]+$/', $cateType)) {
            $this->line('Error: Tên cate type không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ cate type name: '.$cateType);
            return self::ERROR;
        }

        $folder = \Theme::name();

        return $this->creatFile($folder, $postType, $cateType);
    }

    public function creatFile($folder, $file, $cateType): bool
    {
        $path = $folder.'/theme-custom/taxonomies/'.$file.'.taxonomy.php';

        if(file_exists('views/'.$path)) {
            $this->line('Error: file taxonomy views/'.$path.' is exits.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$path);
            return self::ERROR;
        }

        $samplePath = 'plugins/DevTool/sample/taxonomy.php';

        if(!file_exists('views/'.$samplePath)) {
            $this->line('Error: file taxonomy sample not found.');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ views/'.$samplePath);
            return self::ERROR;
        }

        if(!empty($cateType)) {

            $sampleCatePath = 'plugins/DevTool/sample/taxonomy-category.php';

            if(!file_exists('views/'.$sampleCatePath)) {
                $this->line('Error: file taxonomy sample not found.');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ views/'.$sampleCatePath);
                return self::ERROR;
            }
        }

        $storage = \Storage::disk('views');

        if(!file_exists('views/'.$folder.'/theme-custom/taxonomies')) {
            mkdir('views/'.$folder.'/theme-custom/taxonomies', 0775);
        }

        $fileSample = $storage->get($samplePath);

        $fileSample = str_replace('TAXONOMY_POST_TYPE', $file, $fileSample);

        if(!empty($cateType)) {
            $fileCateSample = $storage->get($sampleCatePath);
            $fileCateSample = str_replace('TAXONOMY_POST_TYPE', $file, $fileCateSample);
            $fileCateSample = str_replace('TAXONOMY_CATE_TYPE', $cateType, $fileCateSample);
            $fileSample     = str_replace('TAXONOMY_CATE_TYPE', "'taxonomies' => ['".$cateType."'],", $fileSample);
            $fileSample     = $fileSample."\n".$fileCateSample;
        }
        else {
            $fileSample = str_replace('TAXONOMY_CATE_TYPE', '', $fileSample);
        }

        if($storage->put($path, $fileSample)) {

            $this->line(function (Message $message) use ($file) {
                $message->line('success!', 'green');
                $message->line('taxonomy '.$file.' is created');
            });

            return self::SUCCESS;
        }

        $this->line('Error: Tạo file taxonomy thất bại kiểm tra lại quyền đọc ghi thư mục');

        return self::ERROR;
    }
}
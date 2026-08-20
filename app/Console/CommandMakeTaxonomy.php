<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use DevTool\Supports\DevtoolHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SkillDo\Cms\Plugin\Plugin;
use SkillDo\Support\Path;

class CommandMakeTaxonomy extends Command {

    protected string $signature = 'make:taxonomy {postType} {cateType?} {--plugin=}';

    protected string $description = 'Generates taxonomy service + bootstrap hook';

    public function handle(): bool
    {
        $postType = $this->argument('postType');

        if(!preg_match('/^[a-zA-Z0-9-_]+$/', (string) $postType))
        {
            $this->line('Error: Tên post type không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ post type name: '.$postType);
            return self::ERROR;
        }

        $cateType = $this->argument('cateType');

        if(!empty($cateType) && !preg_match('/^[a-zA-Z0-9-_]+$/', (string) $cateType))
        {
            $this->line('Error: Tên cate type không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ cate type name: '.$cateType);
            return self::ERROR;
        }

        $pluginName = $this->option('plugin');

        /*
         * v8 không còn quét thư mục theme-custom/taxonomies nữa.
         * Taxonomy phải là 1 Service được gọi trong hook init từ bootstrap.
         */
        if(!empty($pluginName) && is_string($pluginName))
        {
            $plugin = Plugin::get($pluginName);

            if(empty($plugin))
            {
                $this->line('Error: plugin not found.');
                $this->line('+ '.$this->fullCommand());
                return self::ERROR;
            }

            return $this->creatFile(
                Storage::disk('plugins'),
                $plugin->getId(),
                DevtoolHelper::pluginAlias($plugin).'\Services',
                $postType,
                $cateType
            );
        }

        return $this->creatFile(
            Storage::disk('views'),
            config('cms.theme.child'),
            'Theme\Services',
            $postType,
            $cateType
        );
    }

    public function creatFile($storage, string $base, string $namespace, string $postType, $cateType): bool
    {
        $class = Str::studly($postType).'TaxonomyService';

        $paths = [
            'service'   => $base.'/app/Services/'.$class.'.php',
            'bootstrap' => $base.'/bootstrap/taxonomy-'.Str::slug($postType).'.php',
        ];

        foreach ($paths as $path)
        {
            if($storage->has($path))
            {
                $this->line('Error: file taxonomy '.$path.' is exits.');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ '.$path);
                return self::ERROR;
            }
        }

        $samples = [
            'service'   => Path::plugin('DevTool/sample/taxonomy-service.php'),
            'post'      => Path::plugin('DevTool/sample/taxonomy.php'),
            'bootstrap' => Path::plugin('DevTool/sample/taxonomy-bootstrap.php'),
        ];

        if(!empty($cateType))
        {
            $samples['category'] = Path::plugin('DevTool/sample/taxonomy-category.php');
        }

        foreach ($samples as $samplePath)
        {
            if(!file_exists($samplePath))
            {
                $this->line('Error: file taxonomy sample not found.');
                $this->line('+ '.$this->fullCommand());
                $this->line('+ '.$samplePath);
                return self::ERROR;
            }
        }

        //thân method register()
        $body = Str::of(file_get_contents($samples['post']))
            ->replace('TAXONOMY_POST_TYPE', $postType)
            ->replace('TAXONOMY_POST_CAP', Str::snake(Str::camel($postType)))
            ->toString();

        if(!empty($cateType))
        {
            $body .= Str::of(file_get_contents($samples['category']))
                ->replace('TAXONOMY_POST_TYPE', $postType)
                ->replace('TAXONOMY_CATE_TYPE', $cateType)
                ->replace('TAXONOMY_CATE_CAP', Str::snake(Str::camel($cateType)))
                ->toString();
        }

        $service = Str::of(file_get_contents($samples['service']))
            ->replace('TAXONOMY_NAMESPACE', $namespace)
            ->replace('TAXONOMY_CLASS_NAME', $class)
            ->replace('//TAXONOMY_BODY', rtrim($body, "\n"))
            ->toString();

        $bootstrap = Str::of(file_get_contents($samples['bootstrap']))
            ->replace('TAXONOMY_NAMESPACE', $namespace)
            ->replace('TAXONOMY_CLASS_NAME', $class)
            ->toString();

        if(!$storage->put($paths['service'], $service))
        {
            $this->line('Error: Tạo file taxonomy service thất bại kiểm tra lại quyền đọc ghi thư mục');
            return self::ERROR;
        }

        if(!$storage->put($paths['bootstrap'], $bootstrap))
        {
            $storage->delete($paths['service']);
            $this->line('Error: Tạo file bootstrap taxonomy thất bại kiểm tra lại quyền đọc ghi thư mục');
            return self::ERROR;
        }

        $this->line(function (Message $message) use ($class, $postType) {
            $message->line('success!', 'green');
            $message->line('taxonomy');
            $message->line($postType, 'blue');
            $message->line('is created ('.$class.' + bootstrap hook init)');
        });

        return self::SUCCESS;
    }
}

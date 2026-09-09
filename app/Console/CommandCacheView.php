<?php
namespace DevTool\Console;



use SkillDo\Support\Path;

class CommandCacheView extends \DevTool\Commands\Command
{
    protected string $signature = 'cache:view';

    protected string $description = 'Remove an item from the cache views';

    public function handle(): bool
    {
        //ViewResolver xếp file biên dịch vào thư mục con theo namespace
        //(framework/views/sicommerce, .../theme, .../admin...). Bản cũ chỉ
        //unlink ở tầng gốc nên không bao giờ dọn được những thư mục đó.
        $total = $this->clear(Path::storage('framework/views'));

        $this->line(function (\DevTool\Commands\Message $message) use ($total) {
            $message->green('success!');
            $message->line('cache view is clear! ('.$total.' file)');
        });

        return self::SUCCESS;
    }

    protected function clear(string $dir): int
    {
        if(!is_dir($dir)) return 0;

        $total = 0;

        foreach (scandir($dir) as $file)
        {
            if($file == '.') continue;
            if($file == '..') continue;

            $path = $dir.DIRECTORY_SEPARATOR.$file;

            if(is_dir($path))
            {
                $total += $this->clear($path);

                //Bỏ lại thư mục rỗng: ViewResolver tự mkdir khi cần, nhưng xoá
                //đi rồi tạo lại chỉ tổ hỏng quyền trên vài máy chủ.
                continue;
            }

            if(@unlink($path)) $total++;
        }

        return $total;
    }
}
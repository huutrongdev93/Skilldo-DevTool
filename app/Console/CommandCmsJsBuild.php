<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use Illuminate\Support\Facades\Storage;
use SkillDo\Cms\Support\Admin;
use SkillDo\Support\Path;
use Symfony\Component\Process\Process;

/**
 * Gộp + rút gọn (và làm rối) JS của admin.
 *
 * Mỗi gói khai trong bundles(): danh sách file nguồn, đích ghi trong disk `views`, và cờ
 * `obfuscate`. Chỉ làm rối MÃ CỦA MÌNH — thư viện ngoài (codemirror) làm rối vừa vô ích
 * vừa phình file và chạy chậm, nên để nguyên.
 *
 * Bước làm rối gọi `node_modules/javascript-obfuscator` (cùng engine với obfuscator.io) qua
 * Symfony Process, cấu hình ở `obfuscator.config.json` gốc repo. Nếu không có node hoặc gói
 * chưa cài thì gói vẫn được ghi ở dạng đã rút gọn và lệnh báo cảnh báo — KHÔNG bỏ dở giữa chừng,
 * vì file đã rút gọn vẫn chạy đúng, chỉ là không được che.
 *
 * Chạy `cms:build:js --no-obfuscate` khi cần bản đọc được để dò lỗi.
 */
class CommandCmsJsBuild extends Command
{
    protected string $signature = 'cms:build:js {--no-obfuscate}';

    protected string $description = 'Build file js files of admin';

    /** Chạy thẳng file js của gói thay vì .bin/*.cmd để khỏi lệ thuộc nền tảng. */
    protected const OBFUSCATOR = 'node_modules/javascript-obfuscator/bin/javascript-obfuscator';

    protected const OBFUSCATOR_CONFIG = 'obfuscator.config.json';

    protected function bundles(): array
    {
        $assets = Admin::asset()->path();

        $node = 'node_modules/';

        return [
            [
                'output'    => 'admin/assets/js/script.bundle.js',
                'obfuscate' => true,
                'files'     => [
                    $assets.'js/bundle/builder-data-store.js',
                    $assets.'js/bundle/element-builder.js',
                    $assets.'js/bundle/element-builder-studio.js',
                    $assets.'js/bundle/pagination.js',
                    $assets.'js/bundle/confirm.js',
                    $assets.'js/bundle/gallery.js',
                    $assets.'js/bundle/ajax.js',
                    $assets.'js/bundle/user.js',
                    $assets.'js/bundle/slug.js',
                    $assets.'js/bundle/table.js',
                    $assets.'js/bundle/popover.js',
                    $assets.'js/bundle/loading-button.js',
                    $assets.'js/bundle/csrf-refresh.js',
                    $assets.'js/bundle/script.js',
                ],
            ],
            /*
             * JS của iframe xem thử trong trình dựng trang. Trước đây KHÔNG nằm trong lệnh này
             * nên sửa `element-builder-review.js` phải tự gộp và làm rối bằng tay — đó là lý do
             * bản vá phía iframe hay phải viết tạm inline trong blade. Xem BUILDER.md mục 5.
             */
            [
                'output'    => 'admin/assets/js/element-builder-review.bundle.js',
                'obfuscate' => true,
                'files'     => [
                    $assets.'js/bundle/element-builder-review.js',
                ],
            ],
            [
                'output'    => 'admin/assets/js/codemirror.bundle.js',
                'obfuscate' => false,
                'files'     => [
                    $node.'codemirror/lib/codemirror.js',
                    $node.'codemirror/mode/css/css.js',
                    $node.'codemirror/mode/javascript/javascript.js',
                    $node.'codemirror/mode/htmlmixed/htmlmixed.js',
                    $node.'codemirror/addon/hint/css-hint.js',
                    $node.'codemirror/addon/hint/javascript-hint.js',
                    $node.'codemirror/addon/scroll/annotatescrollbar.js',
                    $node.'codemirror/addon/edit/closebrackets.js',
                    $node.'codemirror/addon/edit/matchbrackets.js',
                    $node.'codemirror/keymap/sublime.js',
                ],
            ],
            [
                'output'    => 'admin/assets/js/core.bundle.js',
                'obfuscate' => false,
                'files'     => [
                    $node.'core/lang.js',
                    $node.'core/common.js',
                    $node.'core/form.js',
                ],
            ],
        ];
    }

    public function handle(): bool
    {
        try {

            $storage = Storage::disk('views');

            $obfuscate = !$this->option('no-obfuscate');

            foreach ($this->bundles() as $bundle)
            {
                $minifier = new \MatthiasMullie\Minify\JS();

                $found = 0;

                foreach ($bundle['files'] as $file)
                {
                    if (file_exists($file))
                    {
                        $minifier->add($file);

                        $found++;
                    }
                }

                if (empty($found))
                {
                    $this->line('bỏ qua '.$bundle['output'].' — không thấy file nguồn nào', 'yellow');

                    continue;
                }

                $storage->put($bundle['output'], $minifier->minify());

                if (!$obfuscate || empty($bundle['obfuscate']))
                {
                    $this->line('· '.$bundle['output'].' ('.$found.' file)');

                    continue;
                }

                $error = $this->obfuscate(Path::view($bundle['output']));

                if (!empty($error))
                {
                    $this->line('· '.$bundle['output'].' ('.$found.' file) — chỉ rút gọn: '.$error, 'yellow');

                    continue;
                }

                $this->line('· '.$bundle['output'].' ('.$found.' file) — đã làm rối');
            }

            $this->line('success!', 'green');

            return self::SUCCESS;

        }
        catch (\Exception $e) {

            $this->line($e->getMessage());

            return self::ERROR;
        }
    }

    /**
     * Làm rối một file tại chỗ. Trả về chuỗi lỗi, hoặc null nếu xong.
     *
     * Ghi ra file tạm rồi mới thay: obfuscator đọc và ghi cùng một đường dẫn thì file gốc
     * có thể bị cắt cụt khi nó hỏng giữa chừng, mà đó lại là file admin đang chạy.
     */
    protected function obfuscate(string $path): ?string
    {
        $base = Path::base();

        if (!file_exists($base.'/'.self::OBFUSCATOR))
        {
            return 'chưa cài javascript-obfuscator (npm i -D javascript-obfuscator)';
        }

        if (!file_exists($base.'/'.self::OBFUSCATOR_CONFIG))
        {
            return 'thiếu '.self::OBFUSCATOR_CONFIG;
        }

        // Đuôi PHẢI là .js — javascript-obfuscator coi mọi --output không có đuôi .js là THƯ MỤC
        // và lặng lẽ tạo folder rồi ghi file trùng tên nguồn vào trong, exit code vẫn là 0.
        $temp = $path.'.obf.js';

        // Máy chủ web thường không có node trong PATH — cho phép chỉ đường bằng NODE_BIN trong .env
        $process = new Process([
            env('NODE_BIN', 'node'),
            self::OBFUSCATOR,
            $path,
            '--output', $temp,
            '--config', self::OBFUSCATOR_CONFIG,
        ], $base, null, null, 600);

        $process->run();

        if (!$process->isSuccessful())
        {
            @unlink($temp);

            $message = trim($process->getErrorOutput().' '.$process->getOutput());

            return !empty($message) ? $message : 'javascript-obfuscator lỗi mã '.$process->getExitCode();
        }

        if (!file_exists($temp) || filesize($temp) === 0)
        {
            @unlink($temp);

            return 'javascript-obfuscator không tạo được file kết quả';
        }

        if (!@rename($temp, $path))
        {
            @unlink($temp);

            return 'không thay được file '.basename($path);
        }

        return null;
    }
}

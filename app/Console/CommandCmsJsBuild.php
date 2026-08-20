<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Commands\Message;
use Illuminate\Support\Facades\Storage;
use SkillDo\Cms\Support\Admin;

class CommandCmsJsBuild extends Command
{
    protected string $signature = 'cms:build:js';

    protected string $description = 'Build file js files of admin';

    public function handle(): bool
    {
        try {

            $assets = Admin::asset()->path();

            $libs = [
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
            ];

            $minifier = new \MatthiasMullie\Minify\JS();

            foreach($libs as $file)
            {
                if(file_exists($file))
                {
                    $minifier->add($file);
                }
            }

            $storage = Storage::disk('views');

            $storage->put('admin/assets/js/script.bundle.js', $minifier->minify());

            $node   = 'node_modules/';

            $libs = [
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
            ];

            $minifier = new \MatthiasMullie\Minify\JS();

            foreach($libs as $file)
            {
                if(file_exists($file))
                {
                    $minifier->add($file);
                }
            }

            $storage->put('admin/assets/js/codemirror.bundle.js', $minifier->minify());

            $libs = [
                $node.'core/lang.js',
                $node.'core/common.js',
                $node.'core/form.js',
            ];

            $minifier = new \MatthiasMullie\Minify\JS();

            foreach($libs as $file)
            {
                if(file_exists($file))
                {
                    $minifier->add($file);
                }
            }

            $storage->put('admin/assets/js/core.bundle.js', $minifier->minify());

            $this->line(function (Message $message)
            {
                $message->green('success!');
                $message->line('Build admin js file script.bundle.js');
            });

            return self::SUCCESS;

        }
        catch (\Exception $e) {

            $this->line($e->getMessage());

            return self::ERROR;
        }
    }
}
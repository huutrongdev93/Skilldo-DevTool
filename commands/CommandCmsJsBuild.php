<?php
namespace SkillDo\DevTool\Commands;

use Admin;

class CommandCmsJsBuild extends Command {

    public function paramCheck(): bool
    {
        if(count($this->params) >= 2) {
            return false;
        }
        return true;
    }

    public function run(): void
    {
        if($this->paramsCount == 0) {

            try {

                $assets = Admin::asset()->path();

                $libs = [
                    $assets.'js/pagination.js',
                    $assets.'js/confirm.js',
                    $assets.'js/gallery.js',
                    $assets.'js/ajax.js',
                    $assets.'js/user.js',
                    $assets.'js/popover.js',
                    $assets.'js/loading-button.js',
                    $assets.'js/script.js',
                ];

                $minifier = new \MatthiasMullie\Minify\JS();

                foreach($libs as $file) {
                    if(file_exists($file)) {
                        $minifier->add($file);
                    }
                }

                $storage = \Storage::disk('admin');

                $storage->put('assets/js/script.bundle.js', $minifier->minify());

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

                foreach($libs as $file) {
                    if(file_exists($file)) {
                        $minifier->add($file);
                    }
                }

                $storage->put('assets/js/codemirror.bundle.js', $minifier->minify());

                response()->success('[[;#00ee11;]success!]');

            }
            catch (\Exception $e) {
                response()->error($e->getMessage());
            }
        }

        $this->response();
    }
}
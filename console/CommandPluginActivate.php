<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use CacheHandler;
use Language;
use Plugin;
use Template;

class CommandPluginActivate extends Command
{
    protected string $signature = 'plugin:activate {folder}';

    protected string $description = 'Activate a plugin';

    public function handle(): bool
    {
        $pluginName = $this->argument('folder');

        if(!preg_match('/^[a-zA-Z0-9.-]+$/', $pluginName)) {
            $this->line('Error: Tên plugin không hợp lệ');
            $this->line($this->fullCommand());
            $this->line('+ name: '.$pluginName);
            return self::ERROR;
        }

        if(!Plugin::is($pluginName)) {
            $this->line('plugin '.$pluginName.' not found');
            return self::ERROR;
        }

        if(Plugin::isActive($pluginName)) {
            $this->line('plugin '.$pluginName.' is active');
            return self::ERROR;
        }

        $plugin = new Plugin($pluginName);

        $plugin->include();

        $pluginClass = $plugin->getClass();

        //kiểm tra đối tượng plugin
        if(class_exists($pluginClass)) {

            if($plugin->checkActive())  {
                $this->line(trans('plugin.ajax.active.role'));
                return self::ERROR;
            }

            $currentPlugin = new $pluginClass();

            $errors = [];

            /*
             * Nếu plugin chưa từng kích hoạt thì chạy method active của plugin
             */
            if(!$plugin->isInstalled()) {
                if(method_exists($currentPlugin, 'active')) {
                    $errors = $currentPlugin->active();
                }
            }
            /*
             * Nếu plugin đã từng kích hoạt thì chạy method restart của plugin
             */
            else if(method_exists($currentPlugin, 'restart')) {
                $errors = $currentPlugin->restart();
            }

            if(is_skd_error($errors)) {
                response()->error($errors);
            }

            if($plugin->active()) {

                CacheHandler::flush();

                Template::minifyClear();

                Language::buildJs();

                $this->line(trans('plugin.ajax.active.success', ['name' => $plugin->getLabel()]), 'green');

                return self::SUCCESS;
            }
        }

        $this->line(trans('plugin.ajax.active.exit', ['name' => $plugin->getLabel()]));

        return self::ERROR;
    }
}
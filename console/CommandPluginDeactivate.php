<?php
namespace SkillDo\DevTool\Console;
use CacheHandler;
use Language;
use Plugin;
use SkillDo\DevTool\Commands\Command;
use Template;

class CommandPluginDeactivate extends Command
{
    protected string $signature = 'plugin:deactivate {folder}';

    protected string $description = 'Deactivate a plugin';

    public function handle(): bool
    {
        $pluginName = $this->argument('folder');

        if(!preg_match('/^[a-zA-Z0-9.-]+$/', $pluginName)) {
            $this->line('Error: Tên plugin không hợp lệ');
            $this->line($this->fullCommand());
            $this->line('+ name: '.$pluginName);
            return self::ERROR;
        }

        $plugin = new Plugin($pluginName);

        if(!$plugin->checkActive()) {
            $this->line(trans('plugin.ajax.deactivate.noActive'));
            return self::ERROR;
        }

        $pluginClass = $plugin->getClass();

        //kiểm tra đối tượng plugin
        if(class_exists($pluginClass)) {

            $currentPlugin = new $pluginClass();

            if(method_exists($currentPlugin, 'deactivate')) {
                $currentPlugin->deactivate();
            }

            if($plugin->deactivate()) {

                CacheHandler::flush();

                Template::minifyClear();

                Language::buildJs();

                $this->line(trans('plugin.ajax.deactivate.success', ['name' => $plugin->getLabel()]), 'green');
                return self::SUCCESS;
            }
        }

        $this->line(trans('plugin.ajax.active.exit', ['name' => $plugin->getLabel()]));
        return self::ERROR;
    }
}
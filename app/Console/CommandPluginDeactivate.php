<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Plugin\Plugin;

class CommandPluginDeactivate extends Command
{
    protected string $signature = 'plugin:deactivate {folder}';

    protected string $description = 'Deactivate a plugin';

    public function handle(): bool
    {
        $pluginName = $this->argument('folder');

        if(!preg_match('/^[a-zA-Z0-9._-]+$/', $pluginName))
        {
            $this->line('Error: Tên plugin không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ name: '.$pluginName);
            return self::ERROR;
        }

        if(!Plugin::has($pluginName))
        {
            $this->line('plugin '.$pluginName.' not found');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        $plugin = Plugin::get($pluginName);

        if(!$plugin->checkActive())
        {
            $this->line(strip_tags(trans('admin::plugin.ajax.deactivate.noActive')));
            return self::ERROR;
        }

        $pluginClass = $plugin->getClass();

        if(!class_exists($pluginClass))
        {
            $this->line(strip_tags(trans('admin::plugin.ajax.deactivate.exit', ['name' => $plugin->getLabel()])));
            $this->line('+ class: '.$pluginClass);
            return self::ERROR;
        }

        $currentPlugin = new $pluginClass();

        if(method_exists($currentPlugin, 'deactivate'))
        {
            $currentPlugin->deactivate();
        }

        if(!$plugin->deactivate())
        {
            $this->line(strip_tags(trans('admin::plugin.ajax.deactivate.fail')));
            return self::ERROR;
        }

        Cache::flush();

        cmsClearCache();

        do_action('plugin_deactivate', $pluginName);

        $this->line(strip_tags(trans('admin::plugin.ajax.deactivate.success', ['name' => $plugin->getLabel()])), 'green');

        return self::SUCCESS;
    }
}

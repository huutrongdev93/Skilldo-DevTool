<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use DevTool\Supports\DevtoolHelper;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Plugin\Plugin;
use SkillDo\Cms\Plugin\PluginStore;

class CommandPluginActivate extends Command
{
    protected string $signature = 'plugin:activate {folder}';

    protected string $description = 'Activate a plugin';

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

        if($plugin->checkActive())
        {
            $this->line(strip_tags(trans('admin::plugin.ajax.active.isActive')));
            return self::ERROR;
        }

        /*
         * Plugin chưa active nên chưa nằm trong bản đồ PSR-4 của lõi,
         * phải tự đăng ký autoload trước khi gọi tới class bên trong plugin
         */
        DevtoolHelper::registerInactivePluginAutoload();

        if(!$this->checkDependent($plugin))
        {
            return self::ERROR;
        }

        //include file index.php của plugin
        $plugin->include();

        $pluginClass = $plugin->getClass();

        if(!class_exists($pluginClass))
        {
            $this->line(strip_tags(trans('admin::plugin.ajax.active.exit', ['name' => $plugin->getLabel()])));
            $this->line('+ class: '.$pluginClass);
            return self::ERROR;
        }

        $currentPlugin = new $pluginClass();

        $errors = [];

        /*
         * Nếu plugin chưa từng kích hoạt thì chạy method active của plugin
         */
        if(!$plugin->isInstalled())
        {
            if(method_exists($currentPlugin, 'active'))
            {
                $errors = $currentPlugin->active();
            }
        }
        /*
         * Nếu plugin đã từng kích hoạt thì chạy method restart của plugin
         */
        else if(method_exists($currentPlugin, 'restart'))
        {
            $errors = $currentPlugin->restart();
        }

        if(is_skd_error($errors))
        {
            $this->line($errors->first());
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        if(!$plugin->active())
        {
            $this->line(strip_tags(trans('admin::plugin.ajax.active.fail')));
            return self::ERROR;
        }

        $plugin->setDbVersion($plugin->getVersion());

        Cache::flush();

        cmsClearCache();

        do_action('plugin_active', $pluginName);

        $this->line(strip_tags(trans('admin::plugin.ajax.active.success', ['name' => $plugin->getLabel()])), 'green');

        return self::SUCCESS;
    }

    /**
     * Kiểm tra plugin phụ thuộc trước khi kích hoạt
     */
    protected function checkDependent(Plugin $plugin): bool
    {
        $dependent = $plugin->getDependent();

        if(empty($dependent)) return true;

        $plugins = PluginStore::get('list');

        foreach ($dependent as $pluginKey => $pluginVersion)
        {
            if(!isset($plugins[$pluginKey]) || !$plugins[$pluginKey]->checkActive())
            {
                $this->line(strip_tags(trans('admin::plugin.ajax.dependent', ['name' => $pluginKey])));
                return false;
            }

            if(version_compare($pluginVersion, $plugins[$pluginKey]->getVersion()) == 1)
            {
                $this->line(strip_tags(trans('admin::plugin.ajax.dependent.version', [
                    'name'           => $pluginKey,
                    'version'        => $pluginVersion,
                    'currentVersion' => $plugins[$pluginKey]->getVersion()
                ])));
                return false;
            }
        }

        return true;
    }
}

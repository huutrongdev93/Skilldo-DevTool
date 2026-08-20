<?php
namespace DevTool\Console;
use DevTool\Commands\Command;
use SkillDo\Cms\Plugin\Plugin;
use SkillDo\Cms\Plugin\PluginStore;

class CommandPluginList extends Command
{
    protected string $signature = 'plugin {name=all}';

    protected string $description = 'Show a list of all plugins or information about a single plugin';

    public function handle(): bool
    {
        $pluginName = $this->argument('name');

        if($pluginName == 'all') {

            $this->line('Danh sách plugin');

            $list = [];

            foreach(PluginStore::get('list') as $plugin)
            {
                $list[$plugin->getId()] = $plugin->checkActive() ? '[[;#00ee11;]true]' : 'false';
            }

            $this->messages->data([
                'type'      => 'plugins',
                'plugins'   => $list
            ]);

            return self::SUCCESS;
        }

        if(!empty($pluginName)) {

            if(!Plugin::has($pluginName)) {

                $this->line('plugin '.$pluginName.' not found');

                return self::ERROR;
            }

            $plugin = Plugin::getInfo($pluginName);

            $this->line('Thông tin plugin');

            $this->messages->data([
                'type'      => 'plugin-info',
                'plugin'   => $plugin
            ]);

            return self::SUCCESS;
        }

        return self::ERROR;
    }
}
<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;

class CommandPluginList extends Command
{
    protected string $signature = 'plugin {name=all}';

    protected string $description = 'Show a list of all plugins or information about a single plugin';

    public function handle(): bool
    {
        $pluginName = $this->argument('name');

        if($pluginName == 'all') {

            $this->line('Danh sách plugin');

            $plugins = \Plugin::gets();

            foreach($plugins as $pluginName => $isActive) {
                $plugins[$pluginName] = $isActive ? '[[;#00ee11;]true]' : 'false';
            }

            $this->messages->data([
                'type'      => 'plugins',
                'plugins'   => $plugins
            ]);

            return self::SUCCESS;
        }

        if(!empty($pluginName)) {

            if(!\Plugin::is($pluginName)) {

                $this->line('plugin '.$pluginName.' not found');

                return self::ERROR;
            }

            $plugin = \Plugin::getInfo($pluginName);

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
<?php
namespace SkillDo\DevTool\Commands;

use CacheHandler;
use Language;
use Plugin;
use Template;

class CommandPluginList extends Command {

    public function paramCheck(): bool
    {
        if(count($this->params) > 2) {
            return false;
        }
        return true;
    }

    public function run(): void
    {
        if($this->paramsCount == 0) {

            $this->status = 'success';

            $this->message = 'Danh sách plugin';

            $this->data = [
                'type'      => 'plugins',
                'plugins'   => \Plugin::gets()
            ];

            $this->response();
        }

        if($this->paramsCount == 1) {

            $pluginName = $this->params[0];

            if(!\Plugin::is($pluginName)) {
                response()->error('plugin '.$pluginName.' not found');
            }

            $plugin = \Plugin::getInfo($pluginName);

            response()->success('success!', [
                'type' => 'plugin-info',
                'plugin' => $plugin
            ]);
        }

        $this->response();
    }
}
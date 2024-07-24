<?php
namespace SkillDo\DevTool\Commands;

use CacheHandler;
use Language;
use Plugin;
use Template;

class CommandPluginDeactivate extends Command {

    public function paramCheck(): bool
    {
        if(count($this->params) > 2) {
            return false;
        }
        return true;
    }

    public function run(): void
    {
        if($this->paramsCount == 1) {

            $pluginName = $this->params[0];

            if(!\Plugin::is($pluginName)) {
                response()->error('plugin '.$pluginName.' not found');
            }

            $plugin = new Plugin($pluginName);

            if(!$plugin->checkActive()) {
                response()->error(trans('plugin.ajax.deactivate.noActive'));
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

                    response()->success(trans('plugin.ajax.deactivate.success', ['name' => $plugin->getLabel()]));
                }
            }

            response()->error(trans('plugin.ajax.active.exit', ['name' => $plugin->getLabel()]));
        }

        $this->response();
    }
}
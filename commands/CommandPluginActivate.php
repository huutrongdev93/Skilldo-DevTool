<?php
namespace SkillDo\DevTool\Commands;

use CacheHandler;
use Language;
use Plugin;
use Template;

class CommandPluginActivate extends Command {

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

            if(Plugin::isActive($pluginName)) {
                response()->error('plugin '.$pluginName.' is active');
            }

            $plugin = new Plugin($pluginName);

            $plugin->include();

            $pluginClass = $plugin->getClass();

            //kiểm tra đối tượng plugin
            if(class_exists($pluginClass)) {

                if($plugin->checkActive())  {
                    response()->error(trans('plugin.ajax.active.role'));
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

                    response()->success('[[;#00ee11;]'.trans('plugin.ajax.active.success', ['name' => $plugin->getLabel()]).'!]');
                }
            }

            response()->error(trans('plugin.ajax.active.exit', ['name' => $plugin->getLabel()]));
        }

        $this->response();
    }
}
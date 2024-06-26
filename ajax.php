<?php
use JetBrains\PhpStorm\NoReturn;
use SkillDo\Http\Request;
use SkillDo\DevTool\Commands\Command;

class DevToolAjax {
    #[NoReturn]
    static function cacheClear(Request $request): void
    {
        if($request->isMethod('post')) {

            if($request->type == 'css' || $request->type == 'js') {
                Template::minifyClear($request->type);
            }

            if($request->type == 'cache') {

                $request->session()->remove('widgetReviewOptions');

                CacheHandler::flush();
            }

            response()->success('thành công!');
        }

        response()->error('không thành công');
    }

    #[NoReturn]
    static function debugBar(Request $request): void
    {
        if($request->isMethod('post')) {

            $status = $request->input('status');

            if($status == 'on') {
                DevTool::setConfig('debugBar', 1);
            }

            if($status == 'off') {
                DevTool::setConfig('debugBar', 0);
            }

            response()->success('thành công!');
        }

        response()->error('không thành công');
    }

    #[NoReturn]
    static function debugBarAjax(Request $request): void
    {
        if($request->isMethod('post')) {

            $logs = [];

            if(file_exists('uploads/devtool/query-log.txt')) {

                $logsRaw = file_get_contents('uploads/devtool/query-log.txt');

                $logsRaw = explode("\n", $logsRaw);

                foreach ($logsRaw as $key => $log) {
                    if(Str::isJson($log)) {

                        $log = json_decode($log);

                        $logs[$log->action] = [];

                        foreach ($log->queries as $q) {
                            $time = number_format($q->time/1000, 4);
                            $query = DevtoolHelper::interpolateQuery($q->query, $q->bindings);
                            $logs[$log->action][] = ['sql' => DevtoolHelper::highlightSql($query), 'time' => $time];
                        }
                    }
                }
            }

            response()->success('thành công!', [
                'html' => base64_encode(Plugin::partial('Devtool', 'views/debug-bar/ajax-queries', [
                    'ajax' => $logs
                ]))
            ]);
        }

        response()->error('không thành công');
    }

    #[NoReturn]
    static function saveLayout(Request $request): void
    {
        if($request->isMethod('post')) {

            $result = apply_filters('theme_setting_sidebar_save', ['status' => '']);

            if($result['status'] == 'error') {

                response()->error((!empty($result['message'])) ? $result['message'] : 'không thành công');
            }

            response()->success('thành công!');
        }

        response()->error('không thành công');
    }

    #[NoReturn]
    static function setting(Request $request): void
    {
        if($request->isMethod('post')) {

            $name = $request->input('name');

            $name = trim($name);

            if(empty($name)) {
                response()->error('name is empty!');
            }

            $value = $request->input('value');

            $setting = Option::get('devtool_setting');

            if(empty($setting)) {
                $setting = [];
            }

            $setting[$name] = $value;

            Option::update('devtool_setting', $setting);

            response()->success(trans('ajax.save.success'));
        }

        response()->error('Error: setting save failed!');
    }

    #[NoReturn]
    static function terminal(Request $request): void
    {
        if($request->isMethod('post')) {

            $command = $request->input('command');

            $command = trim($command);

            if(empty($command)) {
                response()->error('Error: command is empty!');
            }

            $commandSave = $command;

            $command = explode(' ', $command);

            $command = array_filter($command, fn($value) => !is_null($value) && $value !== '');

            if(empty($command[0])) {
                response()->error('Error: command is empty!');
            }

            $command[0] = strtolower($command[0]);

            $terminal = Command::make($command);

            if($terminal === false) {
                response()->error($command[0].' : The term \''.$command[0].'\' is not recognized as a valid command. Check spelling of the name, or if a path is included, verify that the path is correct and try again.', [
                    '+ '.$commandSave
                ]);
            }

            if($terminal->paramCheck() === false) {
                response()->error('Error: command parameter(s) is invalid. ', [
                    '+ '.$commandSave
                ]);
            }

            $terminal->run();

            response()->error($command[0].' : The term \''.$command[0].'\' is not recognized as a valid command. Check spelling of the name, or if a path is included, verify that the path is correct and try again.', [
                '+ '.$commandSave
            ]);
        }

        response()->error('Error: command is not found');
    }
}

Ajax::admin('DevToolAjax::cacheClear');
Ajax::admin('DevToolAjax::debugBar');
Ajax::admin('DevToolAjax::debugBarAjax');
Ajax::admin('DevToolAjax::saveLayout');
Ajax::admin('DevToolAjax::setting');
Ajax::admin('DevToolAjax::terminal');
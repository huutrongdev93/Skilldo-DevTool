<?php

use JetBrains\PhpStorm\NoReturn;

class DevToolAjax {
    #[NoReturn]
    static function cacheClear(SkillDo\Request\HttpRequest $request, $model): void
    {
        if($request->isMethod('post')) {

            if($request->type == 'css' || $request->type == 'js') {
                Template::minifyClear($request->type);
            }

            if($request->type == 'cache') {
                CacheHandler::flush();
            }

            response()->success('thành công!');
        }

        response()->error('không thành công');
    }

    #[NoReturn]
    static function debugBar(SkillDo\Request\HttpRequest $request, $model): void
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
    static function debugBarAjax(SkillDo\Request\HttpRequest $request, $model): void
    {
        if($request->isMethod('post')) {

            function highlightSql($sql): array|string|null
            {
                $highlighted = preg_replace(
                    ['/\bselect\b/', '/\bfrom\b/', '/\bwhere\b/', '/\band\b/', '/\bOR\b/', '/\bjoin\b/', '/\bINNER JOIN\b/', '/\bLEFT JOIN\b/', '/\bRIGHT JOIN\b/', '/\border by\b/', '/\bgroup by\b/', '/\blimit\b/'],
                    ['<span style="color: blue;">SELECT</span>', '<span style="color: blue;">FROM</span>', '<span style="color: blue;">WHERE</span>', '<span style="color: blue;">AND</span>', '<span style="color: blue;">OR</span>', '<span style="color: blue;">JOIN</span>', '<span style="color: blue;">INNER JOIN</span>', '<span style="color: blue;">LEFT JOIN</span>', '<span style="color: blue;">RIGHT JOIN</span>', '<span style="color: blue;">ORDER BY</span>', '<span style="color: blue;">GROUP BY</span>', '<span style="color: blue;">LIMIT</span>'],
                    $sql
                );

                // Highlight chuỗi trong dấu ``
                return preg_replace('/`([^`]+)`/', '<span style="color: red;">`$1`</span>', $highlighted);
            }

            function interpolateQuery($query, array $params): array|string|null
            {
                $keys = array();
                $values = $params;

                //build a regular expression for each parameter
                foreach ($params as $key => $value) {
                    if (is_string($key)) {
                        $keys[] = "/:" . $key . "/";
                    } else {
                        $keys[] = '/[?]/';
                    }

                    if (is_string($value))
                        $values[$key] = "'" . $value . "'";

                    if (is_array($value))
                        $values[$key] = implode(',', $value);

                    if (is_null($value))
                        $values[$key] = 'NULL';
                }

                $query = preg_replace($keys, $values, $query, 1, $count);

                return $query;
            }

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
                            $query = interpolateQuery($q->query, $q->bindings);
                            $logs[$log->action][] = ['sql' => highlightSql($query), 'time' => $time];
                        }
                    }
                }
            }

            response()->success('thành công!', [
                'html' => base64_encode(Plugin::getBlade('Devtool/sidebar/views/debug-bar', 'ajax-queries', [
                    'ajax' => $logs
                ]))
            ]);
        }

        response()->error('không thành công');
    }

    #[NoReturn]
    static function saveLayout(SkillDo\Request\HttpRequest $request, $model): void
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
}

Ajax::admin('DevToolAjax::cacheClear');
Ajax::admin('DevToolAjax::debugBar');
Ajax::admin('DevToolAjax::debugBarAjax');
Ajax::admin('DevToolAjax::saveLayout');
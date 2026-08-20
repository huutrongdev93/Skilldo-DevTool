<?php
namespace DevTool\Ajax;

use DevTool;
use DevTool\Commands\Kernel;
use DevTool\Supports\DevtoolHelper;
use DevTool\Commands\Command;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Plugin\Plugin;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Language;
use SkillDo\Cms\Support\Option;
use SkillDo\Cms\Support\Theme;
use SkillDo\Http\Request;
use Illuminate\Support\Facades\Storage;
use SkillDo\Support\Path;
use Illuminate\Support\Str;

class DevToolAjax
{
    static function cacheClear(Request $request): void
    {
        if($request->type == 'minify')
        {
            Cache::delete('theme_custom_css_minify');

            Theme::template()->minifyClear();
        }

        if($request->type == 'cache')
        {
            Cache::cmsRoute()->flush();

            Cache::flush();

            // Clear translation caches (path maps + loaded strings per locale/namespace)
            \SkillDo\Translation\Translation::clearCache();

            //build lại language js
            Language::buildJs();
        }

        if($request->type == 'config')
        {
            if(app()->configurationIsCached())
            {
                unlink(app()->getCachedConfigPath());
            }
        }

        if($request->type == 'autoload')
        {
            cmsClearCacheAutoLoad();
        }

        response()->success('thành công!');
    }

    static function debugBar(Request $request): void
    {
        $status = $request->input('status');

        if($status == 'on')
        {
            DevTool::setConfig('debugBar', 1);
        }

        if($status == 'off')
        {
            DevTool::setConfig('debugBar', 0);
        }

        response()->success('thành công!');
    }

    static function debugBarAjax(Request $request): void
    {
        $logs = [];

        $storage = Storage::disk('uploads');

        if($storage->exists('devtool/query-log.txt')) {

            $logsRaw = explode("\n", $storage->get('devtool/query-log.txt'));

            foreach ($logsRaw as $log) {
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
            'html' => base64_encode(Plugin::partial(DevTool::$name, 'sidebar/queries-ajax', [
                'ajax' => $logs
            ]))
        ]);
    }

    static function saveLayout(Request $request): void
    {
        $layouts = $request->input('layout');

        foreach ($layouts as $pageKey => $page)
        {
            Option::update('layout_'.$pageKey, $page);
        }

        response()->success(trans('ajax.save.success'));
    }

    static function setting(Request $request): void
    {
        $name = $request->input('name');

        $value = $request->input('value');

        $name = trim($name);

        if(empty($name))
        {
            response()->error('name is empty!');
        }

        DevTool::setConfig($name, $value);

        response()->success(trans('ajax.save.success'));
    }

    static function terminalData(Request $request): void
    {
        if(!Admin::isRoot())
        {
            response()->error(trans('ajax.error'));
        }

        $theme = Theme::name();

        $themePathTemp = Storage::disk('views')->allFiles(Theme::name());

        /*
         * Danh sách này chỉ phục vụ autocomplete cho `theme:child:copy`,
         * nên bỏ những thư mục không copy sang theme con được:
         * assets (theme con có assets riêng), language (chỉ nạp từ theme cha),
         * app/helpers (preload theo file, không override được).
         */
        $themePaths = array_values(array_filter($themePathTemp, function ($path) use ($theme)
        {
            return (
                $path != '.gitignore' &&
                !str_starts_with($path, $theme.'/app/helpers/') &&
                !str_starts_with($path, $theme.'/assets/') &&
                !str_starts_with($path, $theme.'/language/') &&
                !str_contains($path, '.git/')
            );
        }));

        $themePaths = array_map(function ($path) use ($theme)
        {
            return str_replace($theme.'/', '', $path);
        }, $themePaths);

        $plugins = array_keys(\SkillDo\Cms\Plugin\PluginStore::get('list'));

        response()->success('lấy thông tin thành công', [
            'themes' => $themePaths,
            'plugins' => $plugins
        ]);
    }

    static function terminal(Request $request): void
    {
        $command = $request->input('command');

        $command = trim($command);

        if(empty($command))
        {
            response()->error('Error: command is empty!');
        }

        $kernel = new Kernel(Path::plugin('DevTool/app/Console'));

        $kernel->run($command);
    }
}
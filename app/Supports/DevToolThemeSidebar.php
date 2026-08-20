<?php
namespace DevTool\Supports;

use DevTool;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Plugin\Plugin;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Theme;
use SkillDo\Support\Path;
use Theme\Supports\ThemeLayout;

class DevToolThemeSidebar
{
    /**
     * @throws Less_Exception_Parser
     */
    static function render(): void
    {
        if(Theme::isReviewWidget())
        {
            exit(1);
        }

        if(request()->ajax())
        {
            exit(1);
        }

        if(!Admin::isRoot())
        {
            exit(1);
        }

        $setting = DevTool::config();

        //form setting
        Plugin::view(DevTool::$name,'sidebar/view', [
            'css'           => self::css(),
            'js'            => self::script(),
            'setting'       => $setting
        ]);
    }

    static function tabInfo(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max(memory_get_peak_usage(), 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        $memory = round($bytes, 2).' '.$units[$pow];

        // ---- CPU load (CLI Linux only) ----
        $cpuLoad = '';
        if (PHP_SAPI === 'cli' && stripos(PHP_OS, 'Linux') !== false)
        {
            $cpuLoad = sys_getloadavg();
            $cpuLoad = implode(" / ", $cpuLoad)."\n";
        }

        // ---- Route current ----
        $routerCurrentTemp = app('router')->getCurrentRoute();

        $router = [
            'controller' => $routerCurrentTemp?->getControllerClass(),
            'uri' => $routerCurrentTemp->uri(),
            'methods' => $routerCurrentTemp->methods(),
            'action' => $routerCurrentTemp->getAction(),
            'parameters' => array_filter($routerCurrentTemp->parameters(), function($k)
            {
                return $k != 'request';
            }, ARRAY_FILTER_USE_KEY),
        ];

        return Plugin::partial('DevTool', 'sidebar/info', [
            'memory' => $memory,
            'cpuLoad' => $cpuLoad,
            'router' => $router,
            'template' => template(),
            'module' => Cms::getData('module'),
        ]);
    }

    static function tabLayout(): string
    {
        $page = Theme::getPage();

        $page = match ($page)
        {
            'home_index' => 'home',
            'page_detail' => 'page',
            'post_index' => 'post_category',
            'post_detail' => 'post',
            default => null
        };

        if(!empty($page))
        {
            $pages = [$page => ThemeLayout::pages($page)];
        }
        else
        {
            $pages = ThemeLayout::pages();
        }

        return Plugin::partial('DevTool', 'sidebar/layouts', [
            'layouts' => ThemeLayout::layouts(),
            'pages' => Arr::wrap($pages),
        ]);
    }

    static function tabHook(): string
    {
        show_r(hookGoble()->filters);

        return Plugin::partial('DevTool', 'sidebar/hooks', ['globalFilter' => [], 'listHook' => []]);
    }

    static function tabQueries(): string
    {
        $total = 0;

        $queries = [];

        if(config('app.debug'))
        {
            $databaseQuery = DB::getQueryLog();

            if (hasItems($databaseQuery))
            {
                foreach ($databaseQuery as $q)
                {
                    $time = number_format($q['time']/1000, 4);

                    $total += $q['time']/1000;

                    $query = DevtoolHelper::interpolateQuery($q['query'], $q['bindings']);

                    $queries[] = ['sql' => DevtoolHelper::highlightSql($query), 'time' => $time];
                }
            }
        }

        return Plugin::partial('DevTool', 'sidebar/queries', [
            'queries' => $queries,
            'total' => $total
        ]);
    }

    static function tabRouter(): string
    {
        $routers = app('router')->getRoutes()->getRoutes();

        $routes = [];

        foreach ($routers as $route)
        {
            $temp['name']     = $route->getName();

            $temp['fullPath'] = DevtoolHelper::highlightParams($route->uri());

            $temp['methods']  = implode(',', $route->methods());

            $temp['methods']  = DevtoolHelper::highlightMethod($temp['methods']);

            $action = $route->getAction();

            $temp['action']  = 'Closure';

            if(!empty($action['controller']))
            {
                $temp['action'] = $action['controller'];
            }

            $routes[] = $temp;
        }

        return Plugin::partial('DevTool', 'sidebar/router', ['routers' => $routes]);
    }

    static function tabIncludes(): void
    {
        $files = get_included_files();

        $files = array_map(function ($file) {
            return str_replace(Path::base(), '', $file);
        }, $files);

        show_r(array_filter($files, function ($file) {
            return !str_starts_with($file, 'vendor') && !str_starts_with($file, 'storage');
        }));
    }

    static function css(): string
    {
        return Cache::remember('devtool_minify_css', 30*24*60, function() {
            return lessToCss(file_get_contents(Path::plugin(DevTool::$name.'/assets/css/devtool.less')));
        });
    }

    static function script(): string
    {
        return file_get_contents(Path::plugin(DevTool::$name.'/assets/js/devtool.js'));
    }
}
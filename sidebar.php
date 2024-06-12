<?php
class DevToolThemeSidebar {
    /**
     * @throws Less_Exception_Parser
     */
    static function render(): void
    {
        if(Template::isHome() && request()->query->get('page') == 'widget-review') {
            exit(1);
        }
        if(request()->ajax()) {
            exit(1);
        }

        global $wp_filter;

        $cms = Cms::get();

        $debugBar = '';

        if(DevTool::config('debugBar') == 1) {
            $profiler = new DebugBar();
            if(!empty($cms->_profiler_sections))
            {
                $profiler->set_sections($cms->_profiler_sections);
            }
            $debugBar = $profiler->run();
        }

        $listHook = apply_filters('setting_sidebar_hook_list', []);

        if(empty($listHook)) {
            $listHook[] = [
                'name' => 'List Hook',
                'list' => array_keys($wp_filter)
            ];
        }

        $routesBuilder = Luthier\RouteBuilder::$routes;

        $routes = [];

        foreach ($routesBuilder as $route) {
            $temp['name']     = $route->getName();
            $temp['fullPath'] = DevtoolHelper::highlightParams($route->getFullPath());
            $temp['methods']  = implode(',', $route->getMethods());
            $temp['methods']  = DevtoolHelper::highlightMethod($temp['methods']);
            $temp['action']   = (is_string($route->getAction())) ? $route->getAction() : 'Closure';
            $routes[] = $temp;
        }

        $setting = Option::get('devtool_setting');

        if(empty($setting)) {
            $setting = [
                'layout' => 'horizontal',
                'theme' => 'dark'
            ];
        }

        $themePathTemp = Storage::make('views/'.Theme::name())->allFiles();

        $themePaths = array_values(array_filter($themePathTemp, function ($path) {
            return (
                $path != '.gitignore' &&
                !str_starts_with($path, '.git/') &&
                !str_starts_with($path, 'admin/') &&
                !str_starts_with($path, 'assets/') &&
                !str_starts_with($path, 'language/') &&
                !str_starts_with($path, 'theme-setting/theme-') &&
                !str_starts_with($path, 'theme-setting/menu') &&
                !str_starts_with($path, 'theme-child') &&
                !str_starts_with($path, 'widget/') &&
                !str_starts_with($path, 'widget-sidebar/') &&
                !str_contains($path, '.git/')
            );
        }));

        //form setting
        Plugin::view(DevTool::$name,'views/sidebar/view', [
            'css'           => self::css(),
            'js'            => self::script(),
            'debugBar'      => $debugBar,
            'globalFilter'  => $wp_filter,
            'listHook'      => $listHook,
            'routers'       => $routes,
            'setting'       => $setting,
            'themePaths'    => $themePaths,
            'plugins'       => array_keys(Plugin::gets()),
        ]);
    }

    /**
     * @throws Less_Exception_Parser
     * @throws Exception
     */
    static function css(): string
    {
        $less = file_get_contents(Path::plugin(DevTool::$name.'/assets/css/devtool.less'));
        return Template::less($less)->getCss();
    }

    static function script(): string
    {
        return file_get_contents(Path::plugin(DevTool::$name.'/assets/js/devtool.js'));
    }
}
add_action('post_system', 'DevToolThemeSidebar::render', 1000);
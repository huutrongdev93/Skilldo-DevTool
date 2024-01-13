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

        Plugin::blade(DevTool::$name.'/sidebar/views', 'view', [
            'css'           => self::css(),
            'js'            => self::script(),
            'debugBar'      => $debugBar,
            'globalFilter'  => $wp_filter,
            'listHook'      => $listHook
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
add_action('cle_footer', 'DevToolThemeSidebar::render', 1000);
add_action('admin_footer', 'DevToolThemeSidebar::render', 1000);
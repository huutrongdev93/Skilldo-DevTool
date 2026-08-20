<?php
if(Admin::isRoot())
{
    add_action('cle_footer', 'DevTool\Supports\DevToolThemeSidebar::render', 1000);
    add_action('admin_footer', 'DevTool\Supports\DevToolThemeSidebar::render', 1000);
    add_filter('admin_theme_header_button', 'DevToolAdmin::headerBar');
}

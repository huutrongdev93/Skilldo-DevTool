<?php
use SkillDo\Validate\MissingRequiredParameterException;

/**
 * Plugin name : DevTool
 * Plugin class : DevTool
 * Plugin uri   : https://sikido.vn
 * Description  : Công cụ phát hành cho lập trình viên
 * Author       : SKDSoftware Dev Team
 * Version      : 1.0.0
 */
class DevTool
{
    static string $name = 'DevTool';

    function __construct()
    {
        include_once 'ajax.php';
        include_once 'sidebar/sidebar.php';
        include_once 'sidebar/debug.php';
    }

    //active plugin
    public function active(): void
    {
        Option::update('devToolConfig', self::config());
    }

    //Gở bỏ plugin
    public function uninstall()
    {
    }

    static function assets(Asset $assets): void
    {
        $header = $assets->location('header');
        $footer = $assets->location('footer');
        $node   = 'node_modules/';
        $header->add('icheck', $node.'icheck/skins/square/blue.css');
        $footer->add('iCheck', $node.'icheck/icheck.min.js');
        $footer->add('form',   $node.'core/form.js');
        $header->add('form',   Admin::asset()->path().'css/less/form.css');
    }

    static function config($key = null)
    {
        $devTool = Option::get('devToolConfig');

        if(!have_posts($devTool)) $devTool = [];

        $devTool += [
            'debugBar' => 1
        ];

        return ($key) ? Arr::get($devTool, $key) : $devTool;
    }

    static function setConfig($key, $value): void
    {
        $devTool = self::config();

        if(isset($devTool[$key])) {

            $devTool[$key] = $value;

            Option::update('devToolConfig', $devTool);
        }
    }
}


add_action('theme_custom_assets', 'DevTool::assets');


new DevTool();

AdminMenu::add('devtool', 'Devtool', 'plugins?page=devtool', [
    'callback' => 'devToolView', //function run
]);

/**
 * @throws MissingRequiredParameterException
 */
function devToolView(): void
{
    show_r(session()->all());
}
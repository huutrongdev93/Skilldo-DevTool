<?php
/**
 * Plugin name : DevTool
 * Plugin class : DevTool
 * Plugin uri   : https://sikido.vn
 * Description  : Công cụ phát hành cho lập trình viên
 * Author       : SKDSoftware Dev Team
 * Version      : 1.0.0
 */
function command_psr4_autoloader($class): void
{
    // replace namespace separators with directory separators in the relative
    // class name, append with .php
	if(str_starts_with($class, "SkillDo\DevTool\Commands\\")) {

        $class = str_replace('SkillDo\DevTool\Commands\\', '', $class);

        $file =  __DIR__ . '/commands/' . $class . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
	}
}

spl_autoload_register('command_psr4_autoloader');

class DevTool
{
    static string $name = 'DevTool';

    function __construct()
    {
        include_once 'ajax.php';
        include_once 'helper.php';
        include_once 'sidebar.php';
        include_once 'debug.php';
        include_once 'terminal.php';
    }

    //active plugin
    public function active(): void
    {
        Storage::makeDirectory('devtool');
        Option::update('devToolConfig', self::config());
    }

    //Gở bỏ plugin
    public function uninstall()
    {
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

if(Auth::check()) {
    new DevTool();
}


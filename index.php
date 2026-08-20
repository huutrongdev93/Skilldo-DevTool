<?php

use Illuminate\Support\Facades\Storage;
use SkillDo\Cms\Support\Option;

class DevTool
{
    static string $name = 'DevTool';

    public function active(): void
    {
        Storage::makeDirectory('devtool');

        Option::update('devToolConfig', self::config());
    }

    static function config($key = null)
    {
        return ($key) ? config('DevTool::theme.'.$key) : config('DevTool::theme');
    }

    static function setConfig($key, $value): void
    {
        $devTool = self::config();

        if(isset($devTool[$key]))
        {
            $devTool[$key] = $value;

            Option::update('devToolConfig', $devTool);

            app('config')->set('DevTool::theme.'.$key, $value);
        }
    }
}


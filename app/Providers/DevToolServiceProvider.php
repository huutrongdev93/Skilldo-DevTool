<?php

namespace DevTool\Providers;

use SkillDo\Cms\Support\Option;
use SkillDo\ServiceProvider;

class DevToolServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $devToolConfig = Option::get('devToolConfig');

        if(hasItems($devToolConfig))
        {
            foreach ($devToolConfig as $configName => $configValue)
            {
                $this->app->make('config')->set("DevTool::theme.{$configName}", $configValue);
            }
        }
    }
}

<?php

use SkillDo\Support\Path;

class PLUGIN_CLASS_NAME
{
    public function active(): void
    {
        \PLUGIN_CLASS_NAME\Services\ActivatorService::activate();
    }

    public function uninstall(): void
    {
        \PLUGIN_CLASS_NAME\Services\DeactivatorService::uninstall();
    }
}
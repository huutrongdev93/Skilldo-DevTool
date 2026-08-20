<?php
namespace AJAX_NAMESPACE;

use SkillDo\Http\Request;

class AJAX_CLASS_NAME
{
    static function handle(Request $request): void
    {
        response()->success(trans('ajax..'));
    }
}

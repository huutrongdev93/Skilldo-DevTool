<?php
use JetBrains\PhpStorm\NoReturn;
use SkillDo\Http\Request;

class AJAX_CLASS_NAME {

    #[NoReturn]
    static function AJAX_METHOD_NAME(Request $request, $model): void
    {
        if ($request->isMethod('post')) {

            response()->success(trans('ajax..'));
        }

        response()->error(trans('ajax..'));
    }
}

Ajax::client('AJAX_CLASS_NAME::AJAX_METHOD_NAME');
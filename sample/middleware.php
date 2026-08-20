<?php
namespace MIDDLEWARE_NAMESPACE;

use Closure;
use SkillDo\Http\Request;

/**
 * Middleware đã được khai báo alias trong manifest (plugin.json / theme.json):
 *
 * "middlewares": { "aliases": { "MIDDLEWARE_ALIAS": "..." } }
 *
 * Dùng trong route: Route::middleware('MIDDLEWARE_ALIAS')->group(...);
 * Muốn chạy cho toàn bộ request thì khai vào "global" hoặc "groups" thay cho "aliases".
 */
class MIDDLEWARE_CLASS_NAME
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}

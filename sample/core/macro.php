<?php
/**
 * MacroClassName
 *
 * File macro được Loader preload tự động (app/Macros), không qua PSR-4
 * nên KHÔNG khai báo namespace/class ở đây.
 *
 * Đối tượng nhận macro là các facade/support có trait Macroable,
 * ví dụ: Str, Arr, Theme, Form, Path, Url, Image...
 *
 * Ví dụ: Str::macro('ucWord', fn(string $string) => ...);
 */

//Str::macro('macroName', function(array $args = []) {
//    //To do...
//});

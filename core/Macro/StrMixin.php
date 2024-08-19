<?php
Str::macro('ucWord', function(string $string, string $separator = ' ') {

    $string = explode($separator, $string);

    foreach ($string as $key => $value) {
        $string[$key] = Str::ucfirst($value);
    }

    return implode($separator, $string);
});
<?php
class DevtoolHelper {

    static function highlightSql($sql): array|string|null
    {
        $highlighted = preg_replace(
            ['/\bselect\b/', '/\bfrom\b/', '/\bwhere\b/', '/\band\b/', '/\bOR\b/', '/\bjoin\b/', '/\bINNER JOIN\b/', '/\bLEFT JOIN\b/', '/\bRIGHT JOIN\b/', '/\border by\b/', '/\bgroup by\b/', '/\blimit\b/'],
            ['<span class="highlight-keyword">SELECT</span>', '<span class="highlight-keyword">FROM</span>', '<span class="highlight-keyword">WHERE</span>', '<span class="highlight-keyword">AND</span>', '<span class="highlight-keyword">OR</span>', '<span class="highlight-keyword">JOIN</span>', '<span class="highlight-keyword">INNER JOIN</span>', '<span class="highlight-keyword">LEFT JOIN</span>', '<span class="highlight-keyword">RIGHT JOIN</span>', '<span class="highlight-keyword">ORDER BY</span>', '<span class="highlight-keyword">GROUP BY</span>', '<span class="highlight-keyword">LIMIT</span>'],
            $sql
        );

        // Highlight chuỗi trong dấu ``
        return preg_replace('/`([^`]+)`/', '<span class="highlight-variable">`$1`</span>', $highlighted);
    }

    static function interpolateQuery($query, array $params): array|string|null
    {
        $keys = array();
        $values = $params;

        //build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = "/:" . $key . "/";
            } else {
                $keys[] = '/[?]/';
            }

            if (is_string($value))
                $values[$key] = "'" . $value . "'";

            if (is_array($value))
                $values[$key] = implode(',', $value);

            if (is_null($value))
                $values[$key] = 'NULL';
        }

        $query = preg_replace($keys, $values, $query, 1, $count);

        return $query;
    }

    static function highlightMethod($string): array|string|null
    {
        return preg_replace(
            [
                '/\bPOST\b/',
                '/\bGET\b/',
                '/\bDELETE\b/',
                '/\bPUT\b/',
                '/\bPATH\b/',
                '/\bHEAD\b/'
            ],
            [
                '<span class="highlight-method-post">POST</span>',
                '<span class="highlight-method-get">GET</span>',
                '<span class="highlight-method-delete">DELETE</span>',
                '<span class="highlight-method-put">PUT</span>',
                '<span class="highlight-method-path">PATH</span>',
                '<span class="highlight-method-head">HEAD</span>',
            ],
            $string
        );
    }

    static function highlightParams($string): array|string|null
    {
        return preg_replace('/{([^}]+)}/', '<span class="highlight-method-post">{$1}</span>', $string);
    }
}
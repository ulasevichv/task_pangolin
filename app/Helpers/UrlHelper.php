<?php
namespace App\Helpers;

use Closure;

class UrlHelper
{
    /**
     * Required to allow raw query parameters (they should be passed as closures).
     *
     * @param array<string,string|Closure> $queryParameters
     * @return string
     */
    public static function getUrlQueryString(array $queryParameters): string
    {
        if (empty($queryParameters)) {
            return '';
        }

        $parts = [];
        foreach ($queryParameters as $name => $value) {
            $parts[] = $name . '=' . (is_callable($value) ? $value() : rawurlencode($value));
        }

        return '?' . implode('&', $parts);
    }
}
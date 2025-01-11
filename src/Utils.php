<?php

declare(strict_types=1);

/**
 * Contains the Utils class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-22
 *
 */

namespace Konekt\Menu;

class Utils
{
    /**
     * Adds a new class to an existing set of classes.
     * Examples.:
     *  - addHtmlClass('nav-link', 'active') turns "nav-link" into "nav-link active"
     *  - addHtmlClass('nav-link active', 'active') -> 'nav-link active' // smart, eh?
     *  - addHtmlClass('active', 'active') -> 'active' // no duplicates
     *  - addHtmlClass('active active', 'active') -> 'active' // it even heals duplicates
     */
    public static function addHtmlClass(?string $existingClasses, string $classToAdd): string
    {
        if (empty($existingClasses)) {
            return $classToAdd;
        }

        $classes = trim(trim($existingClasses) . ' ' . trim($classToAdd));

        return implode(' ', array_unique(explode(' ', $classes)));
    }

    public static function isAbsoluteUrl(string $url): bool
    {
        return (bool) parse_url($url, PHP_URL_HOST);
    }

    /**
     * Converts attributes to html string.
     * Eg.: ['disabled', ['src' => 'img.png']] -> ' disabled src="img.png"'
     */
    public static function attrsToHtml(array $attributes): string
    {
        $attrs = [];

        foreach ($attributes as $key => $value) {
            $element = is_numeric($key) ?
                (string) $value :
                (is_null($value) ? (string) $key : $key . '="' . e($value) . '"');
            if (!empty($element)) {
                $attrs[] = $element;
            }
        }

        return count($attrs) ? ' ' . implode(' ', $attrs) : '';
    }
}

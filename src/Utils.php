<?php
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
     *
     * @param $existingClasses
     * @param $classToAdd
     *
     * @return mixed|string
     */
    public static function addHtmlClass($existingClasses, $classToAdd)
    {
        if (empty($existingClasses)) {
            return $classToAdd;
        }

        $classes = trim(trim($existingClasses) . ' ' . trim($classToAdd));

        return implode(' ', array_unique(explode(' ', $classes)));
    }

    /**
     * Returns whether the given url is an absolute one
     *
     * @param $url
     *
     * @return bool
     */
    public static function isAbsoluteUrl($url)
    {
        return parse_url($url, PHP_URL_HOST) ? true : false;
    }

    /**
     * Converts attributes to html string.
     * Eg.: ['disabled', ['src' => 'img.png']] -> ' disabled src="img.png"'
     *
     * @param array $attributes
     *
     * @return string
     */
    public static function attrsToHtml(array $attributes)
    {
        $attrs = [];

        foreach ($attributes as $key => $value) {
            $element = is_numeric($key) ?
                (string)$value :
                (is_null($value) ? (string)$key : $key . '="' . e($value) . '"');
            if (!empty($element)) {
                $attrs[] = $element;
            }
        }

        return count($attrs) ? ' ' . implode(' ', $attrs) : '';
    }
}

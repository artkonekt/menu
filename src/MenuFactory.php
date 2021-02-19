<?php
/**
 * Contains the MenuFactory class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-22
 *
 */

namespace Konekt\Menu;

use View;

class MenuFactory
{
    /**
     * @param string $name
     * @param array  $options
     *
     * @return Menu
     */
    public static function create(string $name, array $options = [])
    {
        $menu = new Menu($name, new MenuConfiguration($options));

        if (array_key_exists('share', $options)) {
            if (is_bool($options['share'])) { // we should also handle if 'share' => false is passed
                if (true === $options['share']) { // but only actually share if true
                    View::share($menu->name, $menu);
                }
            } else {
                View::share((string)$options['share'], $menu);
            }
        }

        return $menu;
    }
}

<?php

declare(strict_types=1);

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

use Illuminate\Support\Facades\View;
use Konekt\Menu\Exceptions\InvalidMenuConfigurationException;

class MenuFactory
{
    public static function create(string $name, array $options = []): Menu
    {
        $menu = new Menu($name, new MenuConfiguration($options));

        $share = $options['share'] ?? null;
        if (true === $share || 1 === $share) {
            View::share($menu->name, $menu);
        } elseif(is_string($share)) {
            if (!self::isValidVariableName($share)) {
                throw new InvalidMenuConfigurationException("The value of the 'share' configuration '$share' is not a valid variable name.");
            }

            View::share($share, $menu);
        }

        return $menu;
    }

    protected static function isValidVariableName(string $name): bool
    {
        return preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $name) === 1;
    }
}

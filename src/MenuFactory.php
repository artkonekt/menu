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
        $options['shared_as'] = static::processShareConfig($options['share'] ?? null, $name);
        $menu = new Menu($name, new MenuConfiguration($options));

        if (false !== $shareAs = $menu->config->sharedAs) {
            View::share($shareAs, $menu);
        }

        return $menu;
    }

    protected static function isValidVariableName(string $name): bool
    {
        return 1 === preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $name);
    }

    private static function processShareConfig(mixed $setting, string $nameOfTheMenu): string|false
    {
        if (true !== $setting && 1 !== $setting && !is_string($setting)) {
            return false;
        }

        $result = is_string($setting) ? $setting: $nameOfTheMenu;

        if (!self::isValidVariableName($result)) {
            throw new InvalidMenuConfigurationException("It is not possible to share the `$nameOfTheMenu` as `$result` in blade views because `$result` is not a valid PHP variable name.");
        }

        return $result;
    }
}

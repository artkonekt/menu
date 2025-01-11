<?php

declare(strict_types=1);

/**
 * Contains the Menus facade class.
 *
 * @author      Lavary
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-16
 *
 */

namespace Konekt\Menu\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Konekt\Menu\Menu create(string $name, array $options = [])
 * @method static \Konekt\Menu\Menu|null get(string $name)
 * @method static bool has(string $name)
 * @method static \Illuminate\Support\Collection all()
 */
class Menus extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'menu';
    }
}

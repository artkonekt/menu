<?php
/**
 * Contains the Menu facade class.
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
 * @method static create($name, $options = []): \Konekt\Menu\Menu
 * @method static get($name): \Konekt\Menu\Menu|null
 * @method static all(): \Illuminate\Support\Collection
 *
 */
class Menu extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'menu';
    }
}

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
 * @method static \Konekt\Menu\Menu create($name, $options = [])
 * @method static \Konekt\Menu\Menu|null get($name)
 * @method static \Illuminate\Support\Collection all()
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

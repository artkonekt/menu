<?php
/**
 * Contains the Menu class.
 *
 * @author      Lavary
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-16
 *
 */

namespace Konekt\Menu;

class Menu
{

    /**
     * Menu collection
     *
     * @var Illuminate\Support\Collection
     */
    protected $collection;

    /**
     * List of menu entries
     *
     * @var Menu[]
     */
    protected $menu = [];

    /**
     * Initializing the menu builder
     */
    public function __construct()
    {
        // creating a collection for storing menus
        $this->collection = new Collection();
    }


    /**
     * Create a new menu instance
     *
     * @param  string   $name
     * @param  callable $callback
     *
     * @return \Konekt\Menu\Menu
     */
    public function make($name, $callback)
    {
        if (is_callable($callback)) {
            if ( ! array_key_exists($name, $this->menu)) {
                $this->menu[$name] = new Builder($name, $this->loadConf($name));
            }

            // Registering the items
            call_user_func($callback, $this->menu[$name]);

            // Storing each menu instance in the collection
            $this->collection->put($name, $this->menu[$name]);

            // Make the instance available in all views
            \View::share($name, $this->menu[$name]);

            return $this->menu[$name];
        }
    }

    /**
     * Loads and merges configuration data
     *
     * @param  string $name
     *
     * @return array
     */
    public function loadConf($name)
    {
        $options = config('laravel-menu.settings');
        $name    = strtolower($name);

        if (isset($options[$name]) && is_array($options[$name])) {
            return array_merge($options['default'], $options[$name]);
        }

        return $options['default'];
    }

    /**
     * Return Menu instance from the collection by key
     *
     * @param  string $key
     *
     * @return \Konekt\Menu\Item
     */
    public function get($key)
    {
        return $this->collection->get($key);
    }


    /**
     * Return Menu collection
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Alias for getCollection
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        return $this->collection;
    }

}

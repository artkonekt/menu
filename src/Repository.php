<?php
/**
 * Contains the Menu Repository class.
 *
 * @author      Lavary
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-16
 *
 */

namespace Konekt\Menu;

use Konekt\Menu\Exceptions\MenuAlreadyExistsException;

class Repository
{
    /**
     * Menu collection
     *
     * @var \Illuminate\Support\Collection
     */
    protected $menus;

    /**
     * Initializing the menu builder
     */
    public function __construct()
    {
        // creating a collection for storing menus
        $this->menus = collect();
    }


    /**
     * Create a new menu instance
     *
     * @param  string   $name
     * @param  callable $callback
     *
     * @return Repository
     * @throws MenuAlreadyExistsException
     */
    public function create($name, $callback = null)
    {
        if ($this->menus->has($name)) {
            throw new MenuAlreadyExistsException("Can not create menu named `$name` because it already exists");
        }

        $this->menus->put($name, new Builder($name, $this->loadConf($name)));

        if (is_callable($callback)) {
            // Registering the items
            call_user_func($callback, $this->menus->get($name));
        }

        return $this->menus->get($name);
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
        $options = config('menu.settings');
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
     * @return Builder|null
     */
    public function get($key)
    {
        return $this->menus->get($key);
    }


    /**
     * Alias for getCollection
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        return $this->menus;
    }

}

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
use View;

/**
 * Menu Repository class contains several menu instances
 */
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
        $this->menus = collect();
    }

    /**
     * Create a new menu instance
     *
     * @param string    $name       The name of the menu
     * @param array     $options    Set of options
     *
     * @see processOptions() method
     *
     * @return Menu
     * @throws MenuAlreadyExistsException
     */
    public function create($name, $options = [])
    {
        if ($this->menus->has($name)) {
            throw new MenuAlreadyExistsException("Can not create menu named `$name` because it already exists");
        }

        $this->menus->put($name, $instance = MenuFactory::create($name, $options));

        return $instance;
    }

    /**
     * Return Menu instance from the collection by name
     *
     * @param  string $name
     *
     * @return Menu|null
     */
    public function get($name)
    {
        return $this->menus->get($name);
    }

    /**
     * Returns whether repo has menu with name
     *
     * @param  string $name
     *
     * @return Menu|null
     */
    public function has($name)
    {
        return $this->menus->has($name);
    }

    /**
     * Returns all the menus (as collection)
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        return $this->menus;
    }
}

<?php

declare(strict_types=1);

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

use Illuminate\Support\Collection;
use Konekt\Extend\TypedDictionary;
use Konekt\Menu\Exceptions\MenuAlreadyExistsException;

class Repository
{
    protected TypedDictionary $menus;

    public function __construct()
    {
        $this->menus = TypedDictionary::ofClass(Menu::class);
    }

    /**
     * Create a new menu instance
     *
     * @see processOptions() method
     *
     * @throws MenuAlreadyExistsException
     */
    public function create(string $name, array $options = []): Menu
    {
        if ($this->menus->has($name)) {
            throw new MenuAlreadyExistsException("Can not create menu named `$name` because it already exists");
        }

        $this->menus->set($name, $instance = MenuFactory::create($name, $options));

        return $instance;
    }

    public function get(string $name): ?Menu
    {
        return $this->menus->get($name);
    }

    public function has(string $name): bool
    {
        return $this->menus->has($name);
    }

    public function all(): Collection
    {
        return collect($this->menus->all());
    }
}

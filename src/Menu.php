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

use Konekt\Menu\Traits\HasAttributes;
use Konekt\Menu\Traits\Renderable;

/**
 * Menu represents a single menu, that has several items, groups
 */
class Menu
{
    use HasAttributes;
    use Renderable;

    /**
     * @var ItemCollection
     */
    public $items;

    /** @var string The name of the menu */
    public $name;

    /** @var MenuConfiguration */
    public $config;

    /**
     * The reserved attributes.
     *
     * @var array
     */
    protected $reserved = ['route', 'action', 'url', 'prefix', 'parent', 'secure', 'raw'];

    /**
     * Menu constructor
     *
     * @param string            $name
     * @param MenuConfiguration $config
     */
    public function __construct($name, MenuConfiguration $config)
    {
        $this->name   = $name;
        $this->config = $config;
        $this->items  = new ItemCollection();
    }

    /**
     * Adds an item to the menu
     *
     * @param string       $name
     * @param string       $title
     * @param string|array $options
     *
     * @return Item
     */
    public function addItem($name, $title, $options = [])
    {
        $options = is_string($options) ? ['url' => $options] : $options;
        $item    = new Item($this, $name, $title, $options);
        $this->items->addItem($item);

        return $item;
    }

    /**
     * Returns menu item by name
     *
     * @return Item
     */
    public function getItem($name)
    {
        return $this->items->get($name);
    }

    /**
     * Remove a menu item by name
     *
     * @param string $name
     * @param bool   $removeChildren
     *
     * @return bool Returns true if item(s) was/were removed, false if failed
     */
    public function removeItem(string $name, $removeChildren = true)
    {
        if ($removeChildren) {
            if ($item = $this->getItem($name)) {
                $item->children()->each(function ($item) {
                    $this->removeItem($item->name);
                });
            }
        }

        return $this->items->remove($name);
    }

    /**
     * Returns menu item by name
     *
     * @return \Konekt\Menu\Item
     */
    public function __get($prop)
    {
        return $this->items->get($prop);
    }
}

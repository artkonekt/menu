<?php
/**
 * Contains the Menu Item Collection class.
 *
 * @author      Lavary
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-16
 *
 */

namespace Konekt\Menu;

use Illuminate\Support\Collection;
use Konekt\Menu\Exceptions\DuplicateItemNameException;

class ItemCollection extends Collection
{
    /**
     * Alias to addItem. Needed for Laravel 5.8 compatibility
     * @see https://github.com/artkonekt/menu/issues/3
     * @see https://github.com/laravel/framework/pull/27082
     *
     * @param mixed $item
     *
     * @return $this
     * @throws DuplicateItemNameException
     */
    public function add($item)
    {
        return $this->addItem($item);
    }

    /**
     * Add new Item to the collection. Performs check for name uniqueness
     *
     * @param Item $item
     *
     * @return $this
     * @throws DuplicateItemNameException
     */
    public function addItem(Item $item)
    {
        if ($this->has($item->name)) {
            throw new DuplicateItemNameException(
                sprintf(
                    'An item with name `%s` already exists in the menu `%s`',
                    $item->name,
                    $item->menu->name
                )
            );
        }

        return $this->put($item->name, $item);
    }

    /**
     * Remove an item from the list
     *
     * @param Item|string $item The item instance or name
     *
     * @return bool Returns true if the element has been removed, false otherwise
     */
    public function remove($item)
    {
        $key = $item instanceof Item ? $item->name : $item;

        if ($this->has($key)) {
            $this->forget($key);
            return true;
        }

        return false;
    }

    /**
     * Add attributes to a collection of items
     *
     * @return ItemCollection
     */
    public function attr(...$args)
    {
        $this->each(function ($item) use ($args) {
            $item->attr(...$args);
        });

        return $this;
    }

    /**
     * Add meta data to a collection of items
     *
     * @param array $args
     *
     * @return ItemCollection
     */
    public function data(...$args)
    {
        $this->each(function ($item) use ($args) {
            $item->data(...$args);
        });

        return $this;
    }

    /**
     * Appends text or HTML to a collection of items
     *
     * @param  string
     *
     * @return ItemCollection
     */
    public function appendHtml($html)
    {
        $this->each(function ($item) use ($html) {
            $item->title .= $html;
        });

        return $this;
    }

    /**
     * Prepends text or HTML to a collection of items
     *
     * @param string $html
     *
     * @return ItemCollection
     */
    public function prependHtml($html)
    {
        $this->each(function ($item) use ($html) {
            $item->title = $html . $item->title;
        });

        return $this;
    }

    /**
     * Returns items with no parent
     *
     * @return \Illuminate\Support\Collection
     */
    public function roots()
    {
        return $this->filter(function ($item) {
            return !$item->hasParent();
        });
    }

    /**
     * Returns active items
     *
     * @return \Illuminate\Support\Collection
     */
    public function actives()
    {
        return $this->filter(function (Item $item) {
            return $item->isItemOrLinkActive();
        });
    }

    /**
     * Returns the items who have children
     *
     * @return static
     */
    public function haveChild()
    {
        return $this->filter(function ($item) {
            return $item->hasChildren();
        });
    }

    /**
     * Returns the items who have parent
     *
     * @return static
     */
    public function haveParent()
    {
        return $this->filter(function ($item) {
            return $item->hasParent();
        });
    }

    /**
     * Search the items based on an attribute
     *
     * @param string $method
     * @param array  $args
     *
     * @return \Konekt\Menu\ItemCollection
     */
    public function __call($method, $args)
    {
        preg_match('/^[W|w]here([a-zA-Z0-9_]+)$/', $method, $matches);

        if (!$matches) {
            trigger_error('Call to undefined method ' . __CLASS__ . '::' . $method . '()', E_USER_ERROR);
        }

        $attribute = strtolower($matches[1]);
        $value     = $args ? $args[0] : null;

        return $this->filterByProperty($attribute, $value);
    }

    /**
     * @param      $property
     * @param      $value
     *
     * @return static
     */
    protected function filterByProperty($property, $value)
    {
        return $this->filter(function ($item) use ($property, $value) {
            if ($item->hasProperty($property)) {
                return
                    $item->attr($property) == $value
                    ||
                    $item->data($property) == $value;
            }

            return false;
        })->keyBy('name');
    }
}

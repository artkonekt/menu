<?php
/**
 * Contains the Menu Collection class.
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
     * Add new Item to the collection. Performs check for name uniqueness
     *
     * @param Item $item
     *
     * @return $this
     * @throws DuplicateItemNameException
     */
    public function add(Item $item)
    {
        if ($this->has($item->name)) {
            throw new DuplicateItemNameException(
                sprintf('An item with name `%s` already exists in the menu `%s`',
                    $item->name, $item->menu->name
                )
            );
        }

        return $this->put($item->name, $item);
    }

    /**
     * Add attributes to a collection of items
     *
     * @param  mixed
     *
     * @return \Konekt\Menu\ItemCollection
     */
    public function attr()
    {
        $args = func_get_args();

        $this->each(function ($item) use ($args) {
            if (count($args) >= 2) {
                $item->attr($args[0], $args[1]);
            } else {
                $item->attr($args[0]);
            }
        });

        return $this;
    }

    /**
     * Add meta data to a collection of items
     *
     * @param  mixed
     *
     * @return \Konekt\Menu\ItemCollection
     */
    public function data()
    {
        $args = func_get_args();

        $this->each(function ($item) use ($args) {
            if (count($args) >= 2) {
                $item->data($args[0], $args[1]);
            } else {
                $item->data($args[0]);
            }
        });

        return $this;
    }

    /**
     * Appends text or HTML to a collection of items
     *
     * @param  string
     *
     * @return \Konekt\Menu\ItemCollection
     */
    public function append($html)
    {
        $this->each(function ($item) use ($html) {
            $item->title .= $html;
        });

        return $this;
    }

    /**
     * Prepends text or HTML to a collection of items
     *
     * @param  string
     *
     * @return \Konekt\Menu\ItemCollection
     */
    public function prepend($html, $key = null)
    {
        $this->each(function ($item) use ($html) {
            $item->title = $html . $item->title;
        });

        return $this;
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
        $recursive = isset($args[1]) ? $args[1] : false;

        return $this->filterByProperty($attribute, $value, $recursive);
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

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

use URL;

/**
 * Menu represents a single menu, that has several items, groups
 */
class Menu
{

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
        $item = new Item($this, $name, $title, $options);
        $this->items->put($name, $item);

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
     * Returns items with no parent
     *
     * @return \Illuminate\Support\Collection
     */
    public function roots()
    {
        return $this->whereParent();
    }

    /**
     * Filter menu items by user callbacks
     *
     * @param  callable $callback
     *
     * @return \Konekt\Menu\Menu
     */
    public function filter($callback)
    {
        if (is_callable($callback)) {

            $this->items = $this->items->filter($callback);
        }

        return $this;
    }

    /**
     * Sorts the menu based on user's callable
     *
     * @param string|callable $sort_type
     *
     * @return \Konekt\Menu\Menu
     */
    public function sortBy($sort_by, $sort_type = 'asc')
    {
        if (is_callable($sort_by)) {
            $rslt = call_user_func($sort_by, $this->items->toArray());

            if ( ! is_array($rslt)) {
                $rslt = array($rslt);
            }

            $this->items = new ItemCollection($rslt);
        }

        // running the sort proccess on the sortable items
        $this->items = $this->items->sort(function ($f, $s) use ($sort_by, $sort_type) {

            $f = $f->$sort_by;
            $s = $s->$sort_by;

            if ($f == $s) {
                return 0;
            }

            if ($sort_type == 'asc') {
                return $f > $s ? 1 : -1;
            }

            return $f < $s ? 1 : -1;

        });

        return $this;
    }


    /**
     * Generate the menu items as list items using a recursive function
     *
     * @param string $type
     * @param int    $parent
     *
     * @return string
     */
    public function render($type = 'ul', $parent = null, $childrenAttributes = array())
    {
        $items = '';

        $item_tag = in_array($type, array('ul', 'ol')) ? 'li' : $type;

        foreach ($this->whereParent($parent) as $item) {
            $items .= '<' . $item_tag . self::attributes($item->attr()) . '>';

            if ($item->link) {
                $items .= '<a' . self::attributes($item->link->attr()) . ' href="' . $item->url() . '">' . $item->title . '</a>';
            } else {
                $items .= $item->title;
            }

            if ($item->hasChildren()) {
                $items .= '<' . $type . self::attributes($childrenAttributes) . '>';
                $items .= $this->render($type, $item->id);
                $items .= "</{$type}>";
            }

            $items .= "</{$item_tag}>";

            if ($item->divider) {
                $items .= '<' . $item_tag . self::attributes($item->divider) . '></' . $item_tag . '>';
            }
        }

        return $items;
    }

    /**
     * Returns the menu as an unordered list.
     *
     * @return string
     */
    public function asUl($attributes = array(), $childrenAttributes = array())
    {
        return '<ul' . self::attributes($attributes) . '>' . $this->render('ul', null, $childrenAttributes) . '</ul>';
    }

    /**
     * Returns the menu as an ordered list.
     *
     * @return string
     */
    public function asOl($attributes = array(), $childrenAttributes = array())
    {
        return '<ol' . self::attributes($attributes) . '>' . $this->render('ol', null, $childrenAttributes) . '</ol>';
    }

    /**
     * Returns the menu as div containers
     *
     * @return string
     */
    public function asDiv($attributes = array(), $childrenAttributes = array())
    {
        return '<div' . self::attributes($attributes) . '>' . $this->render('div', null,
                $childrenAttributes) . '</div>';
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param  array $attributes
     *
     * @return string
     */
    public static function attributes($attributes)
    {
        $html = array();

        foreach ((array)$attributes as $key => $value) {
            $element = self::attributeElement($key, $value);
            if ( ! is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param  string $key
     * @param  string $value
     *
     * @return string
     */
    protected static function attributeElement($key, $value)
    {
        if (is_numeric($key)) {
            $key = $value;
        }
        if ( ! is_null($value)) {
            return $key . '="' . e($value) . '"';
        }
    }

    /**
     * Filter items recursively
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return \Konekt\Menu\ItemCollection
     */
    public function filterRecursive($attribute, $value)
    {
        $collection = new ItemCollection();

        // Iterate over all the items in the main collection
        $this->items->each(function ($item) use ($attribute, $value, &$collection) {

            if ( ! $this->hasProperty($attribute)) {
                return false;
            }

            if ($item->$attribute == $value) {

                $collection->push($item);

                // Check if item has any children
                if ($item->hasChildren()) {

                    $collection = $collection->merge($this->filterRecursive($attribute, $item->id));
                }
            }

        });

        return $collection;
    }

    /**
     * Search the menu based on an attribute
     *
     * @param string $method
     * @param array  $args
     *
     * @return \Konekt\Menu\Item
     */
    public function __call($method, $args)
    {
        preg_match('/^[W|w]here([a-zA-Z0-9_]+)$/', $method, $matches);

        if ($matches) {
            $attribute = strtolower($matches[1]);
        } else {
            trigger_error('Call to undefined method ' . __CLASS__ . '::' . $method . '()', E_USER_ERROR);
        }

        $value     = $args ? $args[0] : null;
        $recursive = isset($args[1]) ? $args[1] : false;

        if ($recursive) {
            return $this->filterRecursive($attribute, $value);
        }

        return $this->items->filter(function ($item) use ($attribute, $value) {

            if ( ! $item->hasProperty($attribute)) {
                return false;
            }

            if ($item->$attribute == $value) {
                return true;
            }

            return false;

        })->values();
    }

    /**
     * Returns menu item by name
     *
     * @return \Konekt\Menu\Item
     */
    public function __get($prop)
    {
        return $this->whereNickname($prop)
                    ->first();
    }

}

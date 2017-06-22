<?php
/**
 * Contains the Menu Item class.
 *
 * @author      Lavary
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-16
 *
 */

namespace Konekt\Menu;

class Item
{

    /** @var string The name (or id) of the menu item */
    public $name;

    /** @var string */
    public $title;

    /** @var Item   Parent item, if any */
    public $parent;

    /** @var bool   Flag for active state */
    public $isActive = false;

    /** @var array */
    public $attributes = [];

    /** @var array  Extra information attached to the menu item */
    protected $data = [];

    /** @var Menu   Reference to the menu holding the item */
    protected $menu;

    private $reserved = ['route', 'action', 'url', 'prefix', 'parent'];

    /**
     * Class constructor
     *
     * @param  Menu   $menu
     * @param  string $name
     * @param  string $title
     * @param  array  $options
     */
    public function __construct(Menu $menu, $name, $title, $options)
    {
        $this->menu       = $menu;
        $this->name       = $name;
        $this->title      = $title;
        $this->attributes = array_except($options, $this->reserved);
        $this->parent     = array_get($options, 'parent', null);


        $path       = array_only($options, array('url', 'route', 'action'));
        $this->link = new Link($path, $this->menu->config->activeClass);

        // Activate the item if items's url matches the request URI
        if ($this->menu->config->autoActivate && $this->url() == \Request::url()) {
            $this->activate();
        }
    }

    /**
     * Creates a sub Item
     *
     * @param  string       $title
     * @param  string|array $options
     *
     * @return void
     */
    public function add($title, $options = '')
    {
        if ( ! is_array($options)) {
            $url            = $options;
            $options        = array();
            $options['url'] = $url;
        }

        $options['parent'] = $this->id;

        return $this->menu->add($title, $options);
    }

    /**
     * Add a plain text item
     *
     * @return \Konekt\Menu\Item
     */
    public function raw($title, array $options = array())
    {
        $options['parent'] = $this->id;

        return $this->menu->raw($title, $options);
    }

    /**
     * Insert a seprator after the item
     *
     * @param array $attributes
     *
     * @return static
     */
    public function divide($attributes = array())
    {
        $attributes['class'] = Menu::formatGroupClass($attributes, array('class' => 'divider'));
        $this->divider = $attributes;

        return $this;
    }


    /**
     * Group children of the item
     *
     * @param  array    $attributes
     * @param  callable $closure
     *
     * @return void
     */
    public function group($attributes, $closure)
    {
        $this->menu->group($attributes, $closure);
    }

    /**
     * Add attributes to the item
     *
     * @param  mixed
     *
     * @return string|\Konekt\Menu\Item
     */
    public function attr()
    {
        $args = func_get_args();

        if (isset($args[0]) && is_array($args[0])) {
            $this->attributes = array_merge($this->attributes, $args[0]);
            return $this;
        } elseif (isset($args[0]) && isset($args[1])) {
            $this->attributes[$args[0]] = $args[1];
            return $this;
        } elseif (isset($args[0])) {
            return isset($this->attributes[$args[0]]) ? $this->attributes[$args[0]] : null;
        }

        return $this->attributes;
    }

    /**
     * Generate URL for link
     *
     * @return string|null
     */
    public function url()
    {
        if (!$this->link) {
            return null;
        }

        return $this->link->url();
    }


    /**
     * Prepends text or html to the item
     *
     * @return \Konekt\Menu\Item
     */
    public function prepend($html)
    {
        $this->title = $html . $this->title;

        return $this;
    }

    /**
     * Appends text or html to the item
     *
     * @return \Konekt\Menu\Item
     */
    public function append($html)
    {
        $this->title .= $html;

        return $this;
    }

    /**
     * Checks if the item has any children
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return count($this->menu->whereParent($this->id)) or false;
    }

    /**
     * Returns childeren of the item
     *
     * @return \Konekt\Menu\ItemCollection
     */
    public function children()
    {
        return $this->menu->whereParent($this->id);
    }

    /**
     * Returns all childeren of the item
     *
     * @return \Konekt\Menu\ItemCollection
     */
    public function all()
    {
        return $this->menu->whereParent($this->id, true);
    }

    /**
     * Sets the item as active
     */
    public function activate()
    {
        if ($this->menu->config->activeElement == 'item') {
            $this->active();
        } else {
            $this->link->activate();
        }

        // If parent activation is enabled:
        if ($this->menu->config->activateParents) {
            // Moving up through the parent nodes, activating them as well.
            if ($this->parent) {
                $this->parent->activate();
            }
        }
    }

    /**
     * Make the item active
     *
     * @return Item
     */
    public function active($pattern = null)
    {
        if ( ! is_null($pattern)) {
            $pattern = ltrim(preg_replace('/\/\*/', '(/.*)?', $pattern), '/');
            if (preg_match("@^{$pattern}\z@", \Request::path())) {
                $this->activate();
            }
            return $this;
        }

        $this->attributes['class'] = Menu::formatGroupClass(
            ['class' => $this->menu->config->activeClass],
            $this->attributes
        );

        $this->isActive = true;

        return $this;
    }

    /**
     * Set or get items's meta data
     *
     * @return string|Item|array
     */
    public function data()
    {
        $args = func_get_args();

        if (isset($args[0]) && is_array($args[0])) {
            $this->data = array_merge($this->data, array_change_key_case($args[0]));

            // Cascade data to item's children if cascade_data option is enabled
            if ($this->menu->conf['cascade_data']) {
                $this->cascade_data($args);
            }
            return $this;
        } elseif (isset($args[0]) && isset($args[1])) {
            $this->data[strtolower($args[0])] = $args[1];

            // Cascade data to item's children if cascade_data option is enabled
            if ($this->menu->conf['cascade_data']) {
                $this->cascade_data($args);
            }
            return $this;
        } elseif (isset($args[0])) {
            return isset($this->data[$args[0]]) ? $this->data[$args[0]] : null;
        }

        return $this->data;
    }

    /**
     * Cascade data to children
     *
     * @param  array $args
     *
     * @return bool
     */
    public function cascade_data($args = array())
    {
        if ( ! $this->hasChildren()) {
            return false;
        }

        if (count($args) >= 2) {
            $this->children()->data($args[0], $args[1]);
        } else {
            $this->children()->data($args[0]);
        }
    }

    /**
     * Check if property exists either in the class or the meta collection
     *
     * @param  string $property
     *
     * @return bool
     */
    public function hasProperty($property)
    {
        if (property_exists($this, $property) || ! is_null($this->data($property))) {
            return true;
        }

        return false;
    }


    /**
     * Search in meta data if a property doesn't exist otherwise return the property
     *
     * @param  string
     *
     * @return string
     */
    public function __get($prop)
    {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        }

        return $this->data($prop);
    }

}

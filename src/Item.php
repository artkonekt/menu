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

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Konekt\Menu\Exceptions\MenuItemNotFoundException;
use Konekt\Menu\Traits\HasAttributes;
use Konekt\Menu\Traits\Renderable;
use Request;

class Item
{
    use HasAttributes;
    use Renderable;

    /** @var string The name (or id) of the menu item */
    public $name;

    /** @var string */
    public $title;

    /** @var Item   Parent item, if any */
    public $parent;

    /** @var bool   Flag for active state */
    public $isActive = false;

    /** @var Link|null */
    public $link;

    /** @var array  Extra information attached to the menu item */
    protected $data = [];

    /** @var Menu   Reference to the menu holding the item */
    protected $menu;

    /** @var array Stack of authorizations */
    protected $authorizationStack = [];

    /** @var string URL pattern to match (if no exact match) */
    private $activeUrlPattern;

    private $reserved = ['route', 'action', 'url', 'prefix', 'parent', 'renderer'];

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
        $this->attributes = Arr::except($options, $this->reserved);
        $this->parent     = $this->resolveParent(Arr::get($options, 'parent', null));
        $this->renderer   = Arr::get($options, 'renderer', null);

        $path       = Arr::only($options, array('url', 'route', 'action'));
        if (!empty($path)) {
            $this->link = new Link($path, $this->menu->config->activeClass);
        }

        $this->checkActivation();
    }

    /**
     * Creates a sub Item
     *
     * @param  string       $name
     * @param  string       $title
     * @param  string|array $options
     *
     * @return Item
     */
    public function addSubItem($name, $title, $options = [])
    {
        $options           = is_array($options) ? $options : ['url' => $options];
        $options['parent'] = $this;

        return $this->menu->addItem($name, $title, $options);
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
     * Adds an authorization condition that will be checked against $user->can($permission)
     * @see self::isAllowed
     *
     * @param string $permission
     */
    public function allowIfUserCan(string $permission)
    {
        $this->authorizationStack[] = $permission;
    }

    /**
     * Adds an authorization condition callback.
     * The (current) user will be passed as first argument to the callback during isAllowed()
     * @see self::isAllowed
     *
     * @param callable $permissionChecker
     */
    public function allowIf(callable $permissionChecker)
    {
        $this->authorizationStack[] = $permissionChecker;
    }

    /**
     * Returns whether the menu item is allowed for the user
     *
     * @param Authenticatable $user In case no user is passed Auth::user() will be used
     *
     * @return bool
     */
    public function isAllowed(Authenticatable $user = null): bool
    {
        $user = $user ?: Auth::user();

        foreach ($this->authorizationStack as $auth) {
            if (is_callable($auth)) {
                if (!$auth($user)) {
                    return false;
                }
            } elseif ($user && $user->cannot($auth)) {
                return false;
            }
        }

        return true;
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
     * Returns whether the item has any children
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return (bool) $this->children()->count();
    }

    /**
     * Returns true if either the item or its link is active
     *
     * @return bool
     */
    public function isItemOrLinkActive(): bool
    {
        return $this->isActive || ($this->hasLink() && $this->link->isActive);
    }

    /**
     * Returns true if any of the children is actie
     */
    public function hasActiveChild(): bool
    {
        return (bool) $this->children()->actives()->count();
    }

    /**
     * Returns children of the item
     *
     * @return \Konekt\Menu\ItemCollection
     */
    public function children()
    {
        return $this->menu->items->filter(function ($item) {
            return $item->hasParent() && $item->parent->name == $this->name;
        });
    }

    /**
     * Returns allowed children of the item
     *
     * @return \Konekt\Menu\ItemCollection
     */
    public function childrenAllowed(Authenticatable $user = null)
    {
        return $this->children()->filter(function ($item) use ($user) {
            return $item->isAllowed($user);
        });
    }

    /**
     * Returns whether the item has a parent
     *
     * @return bool
     */
    public function hasParent()
    {
        return (bool)$this->parent;
    }

    /**
     * Sets the item as active
     */
    public function activate()
    {
        if ($this->menu->config->activeElement == 'item') {
            $this->setToActive();
        } else {
            if ($this->link) {
                $this->link->activate();
            }
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
     * Sets the url pattern that if matched, sets the link to active
     *
     * @param string $pattern   Eg.: 'articles/*
     *
     * @return $this
     */
    public function activateOnUrls($pattern)
    {
        $this->activeUrlPattern = $pattern;
        $this->checkActivation();

        return $this;
    }

    /**
     * Set or get items's meta data
     *
     * @param array $args
     *
     * @return mixed
     */
    public function data(...$args)
    {
        if (isset($args[0]) && is_array($args[0])) {
            $this->data = array_merge($this->data, array_change_key_case($args[0]));

            // Cascade data to item's children if cascade_data option is enabled
            if ($this->menu->config->cascadeData) {
                $this->cascadeData(...$args);
            }
            return $this;
        } elseif (isset($args[0]) && isset($args[1])) {
            $this->data[strtolower($args[0])] = $args[1];

            // Cascade data to item's children if cascade_data option is enabled
            if ($this->menu->config->cascadeData) {
                $this->cascadeData(...$args);
            }
            return $this;
        } elseif (isset($args[0])) {
            return isset($this->data[$args[0]]) ? $this->data[$args[0]] : null;
        }

        return $this->data;
    }

    /**
     * Returns whether metadata with given key exists
     *
     * @param $key
     *
     * @return bool
     */
    public function hasData($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Returns whether the item has a link
     *
     * @return bool
     */
    public function hasLink()
    {
        return (bool)$this->link;
    }

    /**
     * Cascade data to children
     *
     * @param  array $args
     */
    public function cascadeData(...$args)
    {
        if ($this->hasChildren()) {
            $this->children()->data(...$args);
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
        return
            property_exists($this, $property)
            ||
            $this->hasAttribute($property)
            ||
            $this->hasData($property);
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

        if ($this->children()->has($prop)) {
            return $this->children()->get($prop);
        }

        return $this->data($prop);
    }

    /**
     * Activate the item if it's enabled in menu config and item's url matches the request URI
     */
    public function checkActivation()
    {
        if ($this->menu->config->autoActivate && $this->currentUrlMatches()) {
            $this->activate();
        }
    }

    /**
     * Make the item active
     *
     * @return Item
     */
    protected function setToActive()
    {
        $this->attributes['class'] = Utils::addHtmlClass(
            Arr::get($this->attributes, 'class'),
            $this->menu->config->activeClass
        );
        $this->isActive = true;

        return $this;
    }

    /**
     * Returns whether the current URL matches this link's URL
     *
     * @return bool
     */
    protected function currentUrlMatches()
    {
        if ($this->url() == Request::url()) { // If URLs are equal, always return true
            return true;
        }

        if ($this->activeUrlPattern) { // If pattern was set, see if it matches
            $pattern = ltrim(preg_replace('/\*/', '(.*)?', $this->activeUrlPattern), '/');

            return preg_match("@^{$pattern}\z@", Request::path());
        }

        return false; // No match
    }

    private function resolveParent($parent)
    {
        if (!$parent || empty($parent)) {
            return null;
        } elseif ($parent instanceof Item) {
            return $parent;
        } elseif ($this->menu->items->has($parent)) {
            return $this->menu->getItem($parent);
        }

        throw new MenuItemNotFoundException(
            sprintf(
                'Item named `%s` could not be found in the `%s` menu',
                (string)$parent,
                $this->menu->name
            )
        );
    }
}

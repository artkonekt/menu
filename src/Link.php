<?php
/**
 * Contains the Menu Link class.
 *
 * @author      Lavary
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-16
 *
 */

namespace Konekt\Menu;

class Link
{

    /**
     * Path Information
     *
     * @var array
     */
    protected $path = [];

    /**
     * Explicit href for the link
     *
     * @var string
     */
    protected $href;

    /**
     * Link attributes
     *
     * @var array
     */
    public $attributes = [];

    /**
     * Flag for active state
     *
     * @var bool
     */
    public $isActive = false;

    /**
     * Class constructor
     *
     * @param  array $path
     */
    public function __construct($path = [])
    {
        $this->path = $path;
    }

    /**
     * Make the anchor active
     *
     * @return static
     */
    public function active()
    {
        $this->attributes['class'] = Menu::formatGroupClass(['class' => 'active'], $this->attributes);
        $this->isActive            = true;

        return $this;
    }

    /**
     * Set Anchor's href property
     *
     * @param $href
     *
     * @return static
     */
    public function href($href)
    {
        $this->href = $href;

        return $this;
    }

    /**
     * Make the url secure
     *
     * @return static
     */
    public function secure()
    {
        $this->path['secure'] = true;

        return $this;
    }

    /**
     * Add attributes to the link
     *
     * @param  mixed
     *
     * @return string|Link|array
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
     * Check for a method of the same name if the attribute doesn't exist.
     *
     * @param  string $prop
     *
     * @return mixed
     */
    public function __get($prop)
    {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        }

        return $this->attr($prop);
    }
}
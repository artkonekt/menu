<?php
/**
 * Contains the HasAttributes trait.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-23
 *
 */

namespace Konekt\Menu\Traits;

use Konekt\Menu\Utils;

trait HasAttributes
{
    /** @var array */
    protected $attributes = [];

    /**
     * Add/retrieve attributes (jquery style)
     *
     * @param array $args
     *
     * @return mixed
     */
    public function attr(...$args)
    {
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
     * Returns whether the item has an attribute with the given name
     *
     * @param $name
     *
     * @return bool
     */
    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * Returns the attributes as html string
     *
     * @return string
     */
    public function attributesAsHtml()
    {
        return Utils::attrsToHtml($this->attributes);
    }
}

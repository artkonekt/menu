<?php

declare(strict_types=1);

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

use Konekt\Menu\HtmlTagAttributes;

trait HasAttributes
{
    public HtmlTagAttributes $attributes;

    /**
     * Add/retrieve attributes (jquery style)
     *
     * @deprecated use the `push()`, `set()`, `get()` and `toArray()` methods of the $attributes property
     */
    public function attr(...$args)
    {
        if (isset($args[0]) && is_array($args[0])) {
            $this->attributes->push($args[0]);

            return $this;
        } elseif (isset($args[0]) && isset($args[1])) {
            $this->attributes->set($args[0], $args[1]);

            return $this;
        } elseif (isset($args[0])) {
            return $this->attributes->get($args[0]);
        }

        return $this->attributes->toArray();
    }

    /**
     * @deprecated use the $attributes->has() method instead
     */
    public function hasAttribute($name)
    {
        return $this->attributes->has($name);
    }

    /**
     * @deprecated use the $attributes->toHtml() method instead
     */
    public function attributesAsHtml(): string
    {
        return $this->attributes->toHtml();
    }
}

<?php

declare(strict_types=1);

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

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Konekt\Extend\TypedDictionary;
use Konekt\Menu\Exceptions\DuplicateItemNameException;
use Traversable;

class ItemCollection implements Countable, IteratorAggregate
{
    protected TypedDictionary $items;

    /**
     * @param Item[] $items
     */
    public function __construct(array $items = [])
    {
        $this->items = TypedDictionary::ofClass(Item::class);
        if (!empty($items)) {
            $this->items->push(
                array_combine(
                    array_map(fn ($item) => $item->name, $items),
                    $items,
                )
            );
        }
    }

    public function where(string $name, mixed $value): ItemCollection
    {
        return $this->filter(fn (Item $item) => $value === $item->data->get($name));
    }

    public function whereAttribute(string $name, mixed $value): ItemCollection
    {
        return $this->filter(fn (Item $item) => $value === $item->attributes->get($name));
    }

    /**
     * Alias to addItem. Needed for Laravel 5.8 compatibility
     * @see https://github.com/artkonekt/menu/issues/3
     * @see https://github.com/laravel/framework/pull/27082
     *
     * @note 2025-01-11 It's not clear why is this still needed, but I keep it as is in v2 as well
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
     * @throws DuplicateItemNameException
     */
    public function addItem(Item $item): self
    {
        if ($this->items->has($item->name)) {
            throw new DuplicateItemNameException(
                sprintf(
                    'An item with name `%s` already exists in the menu `%s`',
                    $item->name,
                    $item->getMenu()->name
                )
            );
        }

        $this->items->set($item->name, $item);

        return $this;
    }

    public function has(string $name): bool
    {
        return $this->items->has($name);
    }

    public function get(string $name): ?Item
    {
        return $this->items->get($name);
    }

    public function count(): int
    {
        return $this->items->count();
    }

    public function isEmpty(): bool
    {
        return 0 === $this->items->count();
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    public function filter(?callable $callback = null): self
    {
        return new static(
            $this->items->filter($callback)->all()
        );
    }

    public function toArray(): array
    {
        return $this->items->toArray();
    }

    /**
     * Remove an item from the list
     *
     * @param Item|string $item The item instance or name
     *
     * @return bool Returns true if the element has been removed, false otherwise
     */
    public function remove(Item|string $item): bool
    {
        $key = $item instanceof Item ? $item->name : $item;

        if ($this->items->has($key)) {
            $this->items->remove($key);

            return true;
        }

        return false;
    }

    /**
     * Set an attribute on each item in the collection
     */
    public function setAttribute(string $name, mixed $value): self
    {
        /** @var Item $item */
        foreach ($this->items as $item) {
            $item->attributes->set($name, $value);
        }

        return $this;
    }

    /**
     * Set metadata on each item in the collection
     */
    public function setData(string $key, mixed $value): self
    {
        /** @var Item $item */
        foreach ($this->items as $item) {
            $item->data->set($key, $value);
        }

        return $this;
    }

    /**
     * Appends text or HTML to the title of each item in the collection
     */
    public function appendHtml(string $html): self
    {
        foreach ($this->items as $item) {
            $item->title .= $html;
        }

        return $this;
    }

    /**
     * Prepends text or HTML to the title of each item in the collection
     */
    public function prependHtml(string $html): self
    {
        foreach ($this->items as $item) {
            $item->title = $html . $item->title;
        }

        return $this;
    }

    public function roots(): static
    {
        return $this->filter(fn (Item $item) => !$item->hasParent());
    }

    public function actives(): static
    {
        return $this->filter(fn (Item $item) => $item->isItemOrLinkActive());
    }

    public function havingChildren(): static
    {
        return $this->filter(fn (Item $item) => $item->hasChildren());
    }

    public function havingParent(): static
    {
        return $this->filter(fn (Item $item) => $item->hasParent());
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items->toArray());
    }
}

<?php

declare(strict_types=1);

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
use Illuminate\Support\Facades\Request;
use Konekt\Extend\Dictionary;
use Konekt\Menu\Exceptions\MenuItemNotFoundException;
use Konekt\Menu\Traits\HasAttributes;
use Konekt\Menu\Traits\Renderable;

class Item
{
    use HasAttributes;
    use Renderable;

    /** The name (or id) of the menu item */
    public readonly string $name;

    public string $title;

    public ?Item $parent = null;

    public bool $isActive = false;

    public ?Link $link = null;

    /** Extra information attached to the menu item */
    public Dictionary $data;

    protected Menu $menu;

    protected array $authorizationStack = [];

    private ?string $activeUrlPattern = null;

    private static array $reserved = ['route', 'action', 'url', 'prefix', 'parent', 'renderer'];

    public function __construct(Menu $menu, string $name, string $title, array $options = [])
    {
        $this->menu = $menu;
        $this->name = $name;
        $this->title = $title;
        $this->attributes = new HtmlTagAttributes(Arr::except($options, self::$reserved));
        $this->data = new Dictionary();
        $this->parent = $this->resolveParent(Arr::get($options, 'parent'));
        $this->renderer = Arr::get($options, 'renderer');

        $path = Arr::only($options, ['url', 'route', 'action']);
        if (!empty($path)) {
            $this->link = new Link($path, $this->menu->config->activeClass);
        }

        $this->checkActivation();
    }

    public function getMenu(): Menu
    {
        return $this->menu;
    }

    public function addSubItem(string $name, string $title, string|array $optionsOrUrl = []): Item
    {
        $options = is_array($optionsOrUrl) ? $optionsOrUrl : ['url' => $optionsOrUrl];
        $options['parent'] = $this;

        return $this->menu->addItem($name, $title, $options);
    }

    public function url(): ?string
    {
        return $this->link?->url();
    }

    /**
     * Adds an authorization condition that will be checked against $user->can($permission)
     *
     * @see self::isAllowed
     */
    public function allowIfUserCan(string $permission): self
    {
        $this->authorizationStack[] = $permission;

        return $this;
    }

    /**
     * Adds an authorization condition callback.
     * The (current) user will be passed as first argument to the callback during isAllowed()
     *
     * @see self::isAllowed
     */
    public function allowIf(callable $permissionChecker): self
    {
        $this->authorizationStack[] = $permissionChecker;

        return $this;
    }

    /**
     * Returns whether the menu item is allowed for the user
     *
     * @param ?Authenticatable $user In case no user is passed, then Auth::user() will be used
     *
     * @return bool
     */
    public function isAllowed(?Authenticatable $user = null): bool
    {
        $user ??= Auth::user();

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

    public function prepend(string $html): self
    {
        $this->title = $html . $this->title;

        return $this;
    }

    public function append(string $html): self
    {
        $this->title .= $html;

        return $this;
    }

    public function hasChild(string $name): bool
    {
        return $this->children()->has($name);
    }

    public function getChildItem(string $name): ?Item
    {
        return $this->children()->get($name);
    }

    public function hasChildren(): bool
    {
        return $this->children()->isNotEmpty();
    }

    public function isItemOrLinkActive(): bool
    {
        return $this->isActive || $this->link?->isActive;
    }

    public function hasActiveChild(): bool
    {
        return $this->children()->actives()->isNotEmpty();
    }

    public function children(): ItemCollection
    {
        return $this->menu->items->filter(function ($item) {
            return $item->hasParent() && $item->parent->name == $this->name;
        });
    }

    public function childrenAllowed(?Authenticatable $user = null): ItemCollection
    {
        return $this->children()->filter(fn (Item $item) => $item->isAllowed($user));
    }

    public function hasParent(): bool
    {
        return null !== $this->parent;
    }

    public function activate(): void
    {
        if ('item' == $this->menu->config->activeElement) {
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
     */
    public function activateOnUrls(string $pattern): self
    {
        $this->activeUrlPattern = $pattern;
        $this->checkActivation();

        return $this;
    }

    /**
     * Set or get items's meta data
     *
     * @deprecated Use the withData, pushData, $data->toArray() and $data->get() methods instead
     *
     * @param array $args
     *
     * @return mixed
     */
    public function data(...$args)
    {
        if (isset($args[0]) && is_array($args[0])) {
            $this->data->push(array_change_key_case($args[0]));

            // Cascade data to item's children if cascade_data option is enabled
            if ($this->menu->config->cascadeData) {
                $this->cascadeData(...$args);
            }
            return $this;
        } elseif (isset($args[0]) && isset($args[1])) {
            $this->data->set($args[0], $args[1]);

            // Cascade data to item's children if cascade_data option is enabled
            if ($this->menu->config->cascadeData) {
                $this->cascadeData(...$args);
            }

            return $this;
        } elseif (isset($args[0])) {
            return $this->data->get($args[0]);
        }

        return $this->data->toArray();
    }

    public function withData(string $key, mixed $value): self
    {
        $this->data->set(strtolower($key), $value);

        // Cascade data to item's children if cascade_data option is enabled
        if ($this->menu->config->cascadeData && $this->hasChildren()) {
            $this->children()->each(fn (Item $child) => $child->withData($key, $value));
        }

        return $this;
    }

    /**
     * @param array<string, mixed> $assocArrayOfData
     */
    public function pushData(array $assocArrayOfData): self
    {
        if (!Arr::isAssoc($assocArrayOfData)) {
            throw new \InvalidArgumentException('Data passed to Item::pushData must be an associative array.');
        }

        foreach ($assocArrayOfData as $key => $value) {
            $this->withData($key, $value);
        }

        return $this;
    }

    public function hasLink(): bool
    {
        return null !== $this->link;
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
            $this->attributes->has($property)
            ||
            $this->data->has($property);
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

    protected function setToActive(): self
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
     */
    protected function currentUrlMatches(): bool
    {
        if ($this->url() == Request::url()) { // If URLs are equal, always return true
            return true;
        }

        if (null !== $this->activeUrlPattern) { // If pattern was set, see if it matches
            $pattern = ltrim(preg_replace('/\*/', '(.*)?', $this->activeUrlPattern), '/');

            return (bool) preg_match("@^{$pattern}\z@", Request::path());
        }

        return false;
    }

    /**
     * Cascade data to children
     *
     * @deprecated
     */
    protected function cascadeData(...$args): void
    {
        if ($this->hasChildren()) {
            $this->children()->data(...$args);
        }
    }


    private function resolveParent(null|string|Item $parent): ?Item
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
                (string) $parent,
                $this->menu->name
            )
        );
    }
}

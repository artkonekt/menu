<?php

declare(strict_types=1);

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

use Illuminate\Support\Arr;
use Konekt\Menu\Traits\HasAttributes;

class Link
{
    use HasAttributes;

    public bool $isActive = false;

    protected array $path = [];

    protected string $activeClass;

    protected ?string $href = null;

    /**
     * Class constructor
     *
     * @param  array $path
     * @param string $activeClass
     */
    public function __construct(array $path = [], string $activeClass = 'active')
    {
        $this->path = $path;
        $this->activeClass = $activeClass;
        $this->attributes = new HtmlTagAttributes();
    }

    public function activate(): self
    {
        $this->isActive = true;
        $this->attributes['class'] = Utils::addHtmlClass(
            Arr::get($this->attributes, 'class', ''),
            $this->activeClass
        );

        return $this;
    }

    public function setHref(string $href): self
    {
        $this->href = $href;

        return $this;
    }

    /**
     * Return the URL for the link
     */
    public function url(): ?string
    {
        if (null !== $this->href) {
            return $this->href;
        } elseif (isset($this->path['url'])) {
            return $this->getUrl();
        } elseif (isset($this->path['route'])) {
            return $this->getRoute();
        } elseif (isset($this->path['action'])) {
            return $this->getControllerAction();
        }

        return null;
    }

    protected function getUrl(): string
    {
        $url = $this->path['url'];

        $uri = is_array($url) ? $url[0] : $url;
        $params = is_array($url) ? array_slice($url, 1) : null;

        if (Utils::isAbsoluteUrl($uri)) {
            return $uri;
        }

        return url($uri, $params);
    }

    protected function getRoute(): string
    {
        $route = $this->path['route'];
        if (is_array($route)) {
            return route($route[0], array_slice($route, 1));
        }

        return route($route);
    }

    protected function getControllerAction(): string
    {
        $action = $this->path['action'];
        if (is_array($action)) {
            return action($action[0], array_slice($action, 1));
        }

        return action($action);
    }
}

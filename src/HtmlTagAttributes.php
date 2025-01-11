<?php

declare(strict_types=1);

namespace Konekt\Menu;

use Konekt\Extend\Dictionary;

final class HtmlTagAttributes extends Dictionary
{
    public function set(string $name, mixed $value = null): void
    {
        parent::set($name, $value);
    }

    public function push(array $attributes): void
    {
        foreach ($attributes as $name => $value) {
            if (is_string($name)) {
                $this->set($name, is_null($value) ? null : (string) $value);
            } elseif (is_int($name) && is_string($value)) {
                $this->set((string) $value);
            }
        }
    }

    public function toHtml(): string
    {
        return Utils::attrsToHtml($this->data);
    }
}

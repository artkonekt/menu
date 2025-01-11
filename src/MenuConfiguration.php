<?php

declare(strict_types=1);

/**
 * Contains the MenuConfiguration class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-22
 *
 */

namespace Konekt\Menu;

use Illuminate\Support\Arr;
use Konekt\Menu\Exceptions\InvalidMenuConfigurationException;

class MenuConfiguration
{
    public const ACTIVE_ELEMENT_TYPES = ['item', 'link'];

    /** Whether to auto activate items based on routes */
    public bool $autoActivate = true;

    /** Whether to activate item's parents as well */
    public bool $activateParents = true;

    /** CSS class name to add on active elements. 'active' by default */
    public string $activeClass = 'active';

    /** 'item' or 'link': Whether the active element is the item (eg. <li>) or the link (<a>). 'link' by default */
    public string $activeElement = 'link';

    /** Whether to automatically copy metadata to child elements. False by default */
    public bool $cascadeData = false;

    public function __construct(array $options = [])
    {
        $this->parseOptions($options);
    }

    private function parseOptions(array $options): void
    {
        $this->autoActivate = Arr::get($options, 'auto_activate', true);
        $this->activateParents = Arr::get($options, 'activate_parents', true);
        $this->activeClass = Arr::get($options, 'active_class', 'active');
        $this->activeElement = strtolower(Arr::get($options, 'active_element', 'item'));
        $this->cascadeData = Arr::get($options, 'cascade_data', false);

        if (!in_array($this->activeElement, self::ACTIVE_ELEMENT_TYPES)) {
            throw new InvalidMenuConfigurationException(
                sprintf(
                    '`%s` is not a valid value for the `active_element` setting. Must be one of: `%s`',
                    $this->activeElement,
                    implode(',', self::ACTIVE_ELEMENT_TYPES)
                )
            );
        }
    }
}

<?php
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


use Konekt\Menu\Exceptions\InvalidMenuConfigurationException;

class MenuConfiguration
{
    const ACTIVE_ELEMENT_TYPES = ['item', 'link'];

    /** @var bool   Whether to auto activate items based on routes. True by default */
    public $autoActivate;

    /** @var bool   Whether to activate item's parents as well. True by default */
    public $activateParents;

    /** @var string CSS class name to add on active elements. 'active' by default */
    public $activeClass;

    /** @var string 'item'|'link': Whether the active element is the item (eg. <li>) or the link (<a>). 'link' by default */
    public $activeElement;

    /** @var bool Whether to cascade data to child elements. True by default */
    public $cascadeData;

    /**
     * MenuConfiguration constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->parseOptions($options);
    }

    private function parseOptions(array $options)
    {
        $this->autoActivate    = array_get($options, 'auto_activate', true);
        $this->activateParents = array_get($options, 'activate_parents', true);
        $this->activeClass     = array_get($options, 'active_class', 'active');
        $this->activeElement   = strtolower(array_get($options, 'active_element', 'item'));
        $this->cascadeData     = array_get($options, 'cascade_data', true);

        if (!in_array($this->activeElement, self::ACTIVE_ELEMENT_TYPES)) {
            throw new InvalidMenuConfigurationException(
                sprintf('`%s` is not a valid value for the `active_element` setting. Must be one of: `%s`',
                    $this->activeElement,
                    implode(',', self::ACTIVE_ELEMENT_TYPES)
                )
            );
        }
    }

}
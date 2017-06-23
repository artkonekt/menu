<?php
/**
 * Contains the ItemRenderer interface.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-23
 *
 */


namespace Konekt\Menu\Contracts;


use Konekt\Menu\Item;

interface ItemRenderer
{
    /**
     * Renders the menu item and returns it as string
     *
     * @param Item $item
     *
     * @return string
     */
    public function render(Item $item);

}
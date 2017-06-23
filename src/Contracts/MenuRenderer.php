<?php
/**
 * Contains the MenuRenderer interface.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-23
 *
 */


namespace Konekt\Menu\Contracts;


use Konekt\Menu\Menu;

interface MenuRenderer
{
    /**
     * Renders the menu and returns it as string
     *
     * @param Menu $menu
     *
     * @return string
     */
    public function render(Menu $menu);

}

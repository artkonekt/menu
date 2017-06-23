<?php
/**
 * Contains the ItemTest.php class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-23
 *
 */


namespace Konekt\Menu\Tests\Unit;


use Konekt\Menu\Menu;
use Konekt\Menu\Tests\TestCase;

class ItemTest extends TestCase
{
    public function testItemActivatesOnUrlPattern()
    {
        /** @var Menu $menu */
        $menu = \Menu::create('main');
        $menu->addItem('home', 'Home', '/');
        $menu->addItem('articles', 'Articles', '/articles')->activateOnUrl('articles/*');

    }

}
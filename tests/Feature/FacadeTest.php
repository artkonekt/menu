<?php
/**
 * Contains the FacadeTest class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-16
 *
 */


namespace Konekt\Menu\Tests\Feature;


use Konekt\Menu\Tests\TestCase;
use Menu; // The Facade

class FacadeTest extends TestCase
{
    public function testMenuCanBeCreatedWithFacade()
    {
        /** @var \Konekt\Menu\Menu $menu */
        $menu = Menu::create('navbar');
        $menu->addItem('home', 'Home');
        $menu->addItem('twitter', 'Twitter', 'http://to.co');

        $this->assertInstanceOf(\Konekt\Menu\Menu::class, $menu);
        $this->assertCount(2, $menu->items);
        $this->assertNull($menu->getItem('home')->url());
        $this->assertEquals('http://to.co', $menu->getItem('twitter')->url());
    }

}
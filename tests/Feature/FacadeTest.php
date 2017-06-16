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


use Konekt\Menu\Builder;
use Konekt\Menu\Tests\TestCase;
use Menu;

class FacadeTest extends TestCase
{
    public function testMenuCanBeCreatedWithFacade()
    {
        $navbar = Menu::create('navbar', function($menu) {
            $menu->add('Home');
        });

        $this->assertInstanceOf(Builder::class, $navbar);
        $this->assertCount(1, $navbar->items);
    }

}
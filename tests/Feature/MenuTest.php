<?php
/**
 * Contains the MenuTest class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-23
 *
 */


namespace Konekt\Menu\Tests\Feature;


use Konekt\Menu\Menu;
use Konekt\Menu\Tests\TestCase;

class MenuTest extends TestCase
{
    /** @var  Menu */
    protected $menu;

    public function setUp()
    {
        parent::setUp();
        $this->menu = \Menu::create('menu');
        $this->menu->addItem('home', 'Home', '/');
        $this->menu->addItem('about', 'About', '/about');
        $this->menu->addItem('contact', 'Contact', '/contact');
    }

    public function testUlRenderer()
    {
        $html = $this->menu->render('ul');
        $this->assertStringStartsWith('<ul', $html);
        $this->assertStringEndsWith("</ul>\n", $html);

        $this->assertContains(
            sprintf('<li class="active"><a href="%s">Home</a>', self::APP_URL),
            $html
        );

        $this->assertContains(
            sprintf('<li><a href="%s/about">About</a>', self::APP_URL),
            $html
        );

        $this->assertContains(
            sprintf('<li><a href="%s/contact">Contact</a>', self::APP_URL),
            $html
        );
        
    }

    public function testOlRenderer()
    {
        $this->menu->renderer = 'ol';

        $html = $this->menu->render();
        $this->assertStringStartsWith('<ol', $html);
        $this->assertStringEndsWith("</ol>\n", $html);

        $this->assertContains(
            sprintf('<li class="active"><a href="%s">Home</a>', self::APP_URL),
            $html
        );

        $this->assertContains(
            sprintf('<li><a href="%s/about">About</a>', self::APP_URL),
            $html
        );

        $this->assertContains(
            sprintf('<li><a href="%s/contact">Contact</a>', self::APP_URL),
            $html
        );

    }

    public function testDivRenderer()
    {
        $this->menu->renderer = 'div';

        $html = $this->menu->render();
        $this->assertStringStartsWith('<div', $html);
        $this->assertStringEndsWith("</div>\n", $html);

        $this->assertContains(
            sprintf('<div class="active"><a href="%s">Home</a>', self::APP_URL),
            $html
        );

        $this->assertContains(
            sprintf('<div><a href="%s/about">About</a>', self::APP_URL),
            $html
        );

        $this->assertContains(
            sprintf('<div><a href="%s/contact">Contact</a>', self::APP_URL),
            $html
        );
    }

    public function testAttributesAreProperlyRendered()
    {
        $this->menu->attr('class', 'nav nav-inverse');
        $this->assertContains('<ul class="nav nav-inverse"', $this->menu->render('ul'));

        $this->menu->home->attr(['disabled']);
        $this->assertContains('<li class="active" disabled', $this->menu->render('ul'));

        $this->menu->about->attr(['disabled', 'readonly' => 1]);
        $this->assertContains('<li disabled readonly="1"', $this->menu->render('ul'));

        $this->menu->contact->link->attr('target', '_blank');
        $this->assertContains(sprintf(
                '<a href="%s/contact" target="_blank"',
                self::APP_URL
            ),
            $this->menu->render('ul')
        );
    }

}
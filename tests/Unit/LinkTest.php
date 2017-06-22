<?php
/**
 * Contains the LinkTest class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-22
 *
 */


namespace Konekt\Menu\Tests\Unit;


use Konekt\Menu\Link;
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase
{
    public function testActivate()
    {
        $link = new Link();
        $link->activate();

        $this->assertEquals('active', $link->attr('class'));
        $this->assertTrue($link->isActive);
    }

    public function testActivateCustomClass()
    {
        $link = new Link([], 'is-active');
        $link->activate();

        $this->assertEquals('is-active', $link->attr('class'));
        $this->assertTrue($link->isActive);
    }

    public function testActivateWithExistingClasses()
    {
        $link = new Link();
        $link->attr('class', 'primary');
        $link->activate();

        $this->assertEquals('primary active', $link->class);
        $this->assertTrue($link->isActive);
    }

    public function testHrefProperty()
    {
        $link = new Link();
        $link->href('/#/about');

        $this->assertEquals('/#/about', $link->url());
    }

    public function testAttrMethod()
    {
        $link = new Link();

        $this->assertNull($link->attr('none'));
        $link->attr('none', 107);
        $this->assertEquals(107, $link->attr('none'));

        $this->assertNull($link->attr('kaboom'));
        $link->attr(['kaboom' => 'crackle']);
        $this->assertEquals('crackle', $link->attr('kaboom'));


    }

    public function testDynamicPropertiesCanBeRead()
    {
        $link = new Link();

        $link->attr('kaboom', 'Borland Delphi');
        $this->assertEquals('Borland Delphi', $link->kaboom);
    }

    public function testDynamicPropertiesCanAlsoBeWritten()
    {
        $link = new Link();

        $link->attr('zoink', 'Krakow');
        $this->assertEquals('Krakow', $link->zoink);

        $link->zoink = 'Warsaw';
        $this->assertEquals('Warsaw', $link->zoink);
        $this->assertEquals('Warsaw', $link->attr('zoink'));
    }

}
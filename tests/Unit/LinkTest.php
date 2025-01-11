<?php

declare(strict_types=1);
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
        $link->attributes->set('class', 'primary');
        $link->activate();

        $this->assertEquals('primary active', $link->attributes->get('class'));
        $this->assertTrue($link->isActive);
    }

    public function testHrefProperty()
    {
        $link = new Link();
        $link->setHref('/#/about');

        $this->assertEquals('/#/about', $link->url());
    }

    public function testTheDeprecatedAttrMethod()
    {
        $link = new Link();

        $this->assertNull($link->attr('none'));
        $link->attr('none', 107);
        $this->assertEquals(107, $link->attr('none'));

        $this->assertNull($link->attr('kaboom'));
        $link->attr(['kaboom' => 'crackle']);
        $this->assertEquals('crackle', $link->attr('kaboom'));
    }
}

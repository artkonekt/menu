<?php

declare(strict_types=1);

/**
 * Contains the ItemTest class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-23
 *
 */

namespace Konekt\Menu\Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Konekt\Menu\Exceptions\MenuItemNotFoundException;
use Konekt\Menu\Facades\Menus;
use Konekt\Menu\Menu;
use Konekt\Menu\Tests\TestCase;

class ItemTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/articles/{slug}', fn ($slug) => 'Hello, ' . $slug);
        Route::get('/about', fn () => 'About Us');
    }

    public function testItemParentCanBeResolvedProperly()
    {
        $menu = Menus::create('uberGigaMenu');
        $about = $menu->addItem('about', 'About', '/about');

        $this->assertEquals($about, $about->addSubItem('who-we-are', 'Who We are', '/who-we-are')->parent);

        $this->assertEquals($about, $menu->getItem('about')->addSubItem('what-we-do', 'What We Do', '/what-we-do')->parent);

        $this->assertEquals(
            $about,
            $menu->addItem('our-goals', 'Our Goals', [
                'parent' => 'about',
                'url' => '/our-goals'
            ])->parent
        );
    }

    public function testInvalidParentThrowsException()
    {
        $menu = Menus::create('whoaa');
        $this->expectException(MenuItemNotFoundException::class);
        $menu->addItem('shh', 'Shh', ['parent' => 'inexistent']);
    }

    public function testOnlyItemGetsActivatedIfActiveElementIsItem()
    {
        $menu = Menus::create('main', ['active_element' => 'item']);
        $home = $menu->addItem('home', 'Home', '/');

        $this->assertTrue($home->isActive);
        $this->assertFalse($home->link->isActive);

        $about = $menu->addItem('about', 'About', '/about');
        $about->activate();
        $this->assertTrue($about->isActive);
        $this->assertFalse($about->link->isActive);
    }

    public function testOnlyLinkGetsActivatedIfActiveElementIsLink()
    {
        $menu = Menus::create('main', ['active_element' => 'link']);
        $home = $menu->addItem('home', 'Home', '/');

        $this->assertFalse($home->isActive);
        $this->assertTrue($home->link->isActive);

        $about = $menu->addItem('about', 'About', '/about');
        $about->activate();

        $this->assertFalse($about->isActive);
        $this->assertTrue($about->link->isActive);
    }

    public function testItemActivatesOnSimpleUrl()
    {
        /** @var Menu $menu */
        $menu = Menus::create('main');

        $this->app['request'] = $this->mockRequest('about');
        $menu->addItem('home', 'Home', '/');
        $menu->addItem('about', 'About', '/about');

        $this->assertTrue($menu->getItem('about')->isActive);
        $this->assertFalse($menu->getItem('home')->isActive);
    }

    public function testItemActivatesOnUrlPattern()
    {
        /** @var Menu $menu */
        $menu = Menus::create('main');

        $this->app['request'] = $this->mockRequest('article/how-to-buy-a-sandwich');
        $menu->addItem('home', 'Home', '/');
        $menu->addItem('about', 'About', '/about');
        $menu->addItem('articles', 'Articles', '/articles')->activateOnUrls('/article/*');

        $this->assertFalse($menu->getItem('about')->isActive);
        $this->assertFalse($menu->getItem('home')->isActive);
        $this->assertTrue($menu->getItem('articles')->isActive);
    }

    public function testItemActivatesOnSelfUrlEvenIfPatternWasSet()
    {
        /** @var Menu $menu */
        $menu = Menus::create('main');

        $this->app['request'] = $this->mockRequest('articles');
        $menu->addItem('home', 'Home', '/');
        $menu->addItem('about', 'About', '/about');
        $menu->addItem('articles', 'Articles', '/articles')->activateOnUrls('/article/*');

        $this->assertTrue($menu->getItem('articles')->isActive);
    }

    /**
     * Returns a request mock with the given path
     * // Even though Taylor states it shouldn't be done, I do
     *
     * @param string $path  Must not contain the trailing slash eg.: "contact"
     *
     * @return \Mockery\MockInterface
     */
    protected function mockRequest($path)
    {
        $result = \Mockery::mock(Request::class);
        $result->shouldReceive('getScheme')->andReturn('http');
        $result->shouldReceive('root')->andReturn(self::APP_URL);
        $result->shouldReceive('url')->andReturn(self::APP_URL . '/' . $path);
        $result->shouldReceive('path')->andReturn($path);
        $result->shouldReceive('setUserResolver');

        return $result;
    }
}

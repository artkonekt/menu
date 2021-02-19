<?php
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
use Konekt\Menu\Exceptions\MenuItemNotFoundException;
use Konekt\Menu\Menu;
use Konekt\Menu\Tests\TestCase;

class ItemTest extends TestCase
{
    public function testItemParentCanBeResolvedProperly()
    {
        $menu = \Menu::create('uberGigaMenu');

        $menu->addItem('about', 'About', '/about');

        $this->assertEquals(
            $menu->about,
            $menu->about->addSubItem('who-we-are', 'Who We are', '/who-we-are')->parent
        );

        $this->assertEquals(
            $menu->about,
            $menu->getItem('about')->addSubItem('what-we-do', 'What We Do', '/what-we-do')->parent
        );

        $this->assertEquals(
            $menu->about,
            $menu->addItem('our-goals', 'Our Goals', [
                'parent' => 'about',
                'url'    => '/our-goals'
            ])->parent
        );
    }

    public function testInvalidParentThrowsException()
    {
        $menu = \Menu::create('whoaa');
        $this->expectException(MenuItemNotFoundException::class);
        $menu->addItem('shh', 'Shh', ['parent' => 'inexistent']);
    }

    public function testOnlyItemGetsActivatedIfActiveElementIsItem()
    {
        $menu = \Menu::create('main', ['active_element' => 'item']);
        $menu->addItem('home', 'Home', '/');

        $this->assertTrue($menu->home->isActive);
        $this->assertFalse($menu->home->link->isActive);

        $menu->addItem('about', 'About', '/about')->activate();
        $this->assertTrue($menu->about->isActive);
        $this->assertFalse($menu->about->link->isActive);
    }

    public function testOnlyLinkGetsActivatedIfActiveElementIsLink()
    {
        $menu = \Menu::create('main', ['active_element' => 'link']);
        $menu->addItem('home', 'Home', '/');

        $this->assertFalse($menu->home->isActive);
        $this->assertTrue($menu->home->link->isActive);

        $menu->addItem('about', 'About', '/about')->activate();
        $this->assertFalse($menu->about->isActive);
        $this->assertTrue($menu->about->link->isActive);
    }

    public function testItemActivatesOnSimpleUrl()
    {
        /** @var Menu $menu */
        $menu = \Menu::create('main');

        $this->app['request'] = $this->mockRequest('about');
        $menu->addItem('home', 'Home', '/');
        $menu->addItem('about', 'About', '/about');

        $this->assertTrue($menu->about->isActive);
        $this->assertFalse($menu->home->isActive);
    }

    public function testItemActivatesOnUrlPattern()
    {
        /** @var Menu $menu */
        $menu = \Menu::create('main');

        $this->app['request'] = $this->mockRequest('article/how-to-buy-a-sandwich');
        $menu->addItem('home', 'Home', '/');
        $menu->addItem('about', 'About', '/about');
        $menu->addItem('articles', 'Articles', '/articles')->activateOnUrls('/article/*');

        $this->assertFalse($menu->about->isActive);
        $this->assertFalse($menu->home->isActive);
        $this->assertTrue($menu->articles->isActive);
    }

    public function testItemActivatesOnSelfUrlEvenIfPatternWasSet()
    {
        /** @var Menu $menu */
        $menu = \Menu::create('main');

        $this->app['request'] = $this->mockRequest('articles');
        $menu->addItem('home', 'Home', '/');
        $menu->addItem('about', 'About', '/about');
        $menu->addItem('articles', 'Articles', '/articles')->activateOnUrls('/article/*');

        $this->assertTrue($menu->articles->isActive);
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

    protected function setUp(): void
    {
        parent::setUp();
        \Route::get('/articles/{slug}', function ($slug) {
            return 'Hello, ' . $slug;
        });

        \Route::get('/about', function () {
            return 'About Us';
        });
    }
}

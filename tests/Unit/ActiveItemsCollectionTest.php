<?php
/**
 * Contains the ActiveItemsCollectionTest class.
 *
 * @copyright   Copyright (c) 2020 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2020-03-15
 *
 */

namespace Konekt\Menu\Tests\Unit;

use Illuminate\Http\Request;
use Konekt\Menu\Menu;
use Konekt\Menu\MenuFactory;
use Konekt\Menu\Tests\TestCase;

class ActiveItemsCollectionTest extends TestCase
{
    /** @test */
    public function has_active_child_returns_false_when_there_are_no_active_items_at_all()
    {
        /** @var Menu $menu */
        $menu = MenuFactory::create('sidebar', ['auto_activate' => true]);

        $parent1 = $menu->addItem('parent1', 'Parent 1', ['url' => '/parent-1']);
        $parent1->addSubItem('parent1-child1', 'Child 1', ['url' => '/parent-1/child-1']);
        $parent1->addSubItem('parent1-child2', 'Child 2', ['url' => '/parent-1/child-2']);

        $parent2 = $menu->addItem('parent2', 'Parent 2', ['url' => '/parent-2']);
        $parent2->addSubItem('parent2-child-a', 'Child A', ['url' => '/parent-2/child-a']);
        $parent2->addSubItem('parent2-child-b', 'Child B', ['url' => '/parent-2/child-b']);

        $this->assertFalse($parent1->hasActiveChild());
        $this->assertFalse($parent2->hasActiveChild());
    }

    /** @test */
    public function has_active_child_returns_false_when_the_item_has_no_active_subitems()
    {
        $this->app['request'] = $this->mockRequest('parent-2/child-a');

        /** @var Menu $menu */
        $menu = MenuFactory::create('sidebar', ['auto_activate' => true]);

        $parent1 = $menu->addItem('parent1', 'Parent 1', ['url' => '/parent-1']);
        $parent1->addSubItem('parent1-child1', 'Child 1', ['url' => '/parent-1/child-1']);
        $parent1->addSubItem('parent1-child2', 'Child 2', ['url' => '/parent-1/child-2']);

        $parent2 = $menu->addItem('parent2', 'Parent 2', ['url' => '/parent-2']);
        $parent2->addSubItem('parent2-child-a', 'Child A', ['url' => '/parent-2/child-a']);
        $parent2->addSubItem('parent2-child-b', 'Child B', ['url' => '/parent-2/child-b']);

        $this->assertFalse($parent1->hasActiveChild());
    }

    /** @test */
    public function has_active_child_returns_false_when_the_item_itself_is_active_but_has_no_active_subitems()
    {
        $this->app['request'] = $this->mockRequest('parent-1');

        /** @var Menu $menu */
        $menu = MenuFactory::create('sidebar', ['auto_activate' => true]);

        $parent1 = $menu->addItem('parent1', 'Parent 1', ['url' => '/parent-1']);
        $parent1->addSubItem('parent1-child1', 'Child 1', ['url' => '/parent-1/child-1']);
        $parent1->addSubItem('parent1-child2', 'Child 2', ['url' => '/parent-1/child-2']);

        $parent2 = $menu->addItem('parent2', 'Parent 2', ['url' => '/parent-2']);
        $parent2->addSubItem('parent2-child-a', 'Child A', ['url' => '/parent-2/child-a']);
        $parent2->addSubItem('parent2-child-b', 'Child B', ['url' => '/parent-2/child-b']);

        $this->assertTrue($parent1->isActive);
        $this->assertFalse($parent1->hasActiveChild());
    }

    /** @test */
    public function has_active_child_returns_true_when_an_item_has_an_active_subitem()
    {
        $this->app['request'] = $this->mockRequest('parent-1/child-1');

        /** @var Menu $menu */
        $menu = MenuFactory::create('sidebar', ['auto_activate' => true]);

        $parent1 = $menu->addItem('parent1', 'Parent 1', ['url' => '/parent-1']);
        $parent1->addSubItem('parent1-child1', 'Child 1', ['url' => '/parent-1/child-1']);
        $parent1->addSubItem('parent1-child2', 'Child 2', ['url' => '/parent-1/child-2']);

        $parent2 = $menu->addItem('parent2', 'Parent 2', ['url' => '/parent-2']);
        $parent2->addSubItem('parent2-child-a', 'Child A', ['url' => '/parent-2/child-a']);
        $parent2->addSubItem('parent2-child-b', 'Child B', ['url' => '/parent-2/child-b']);

        $this->assertTrue($parent1->hasActiveChild());
    }

    /** @test */
    public function has_active_child_returns_true_when_an_item_has_an_active_subitem_and_active_element_is_configured_for_link()
    {
        $this->app['request'] = $this->mockRequest('parent-1/child-1');

        /** @var Menu $menu */
        $menu = MenuFactory::create('sidebar', ['auto_activate' => true, 'active_element' => 'link']);

        $parent1 = $menu->addItem('parent1', 'Parent 1', ['url' => '/parent-1']);
        $parent1->addSubItem('parent1-child1', 'Child 1', ['url' => '/parent-1/child-1']);
        $parent1->addSubItem('parent1-child2', 'Child 2', ['url' => '/parent-1/child-2']);

        $parent2 = $menu->addItem('parent2', 'Parent 2', ['url' => '/parent-2']);
        $parent2->addSubItem('parent2-child-a', 'Child A', ['url' => '/parent-2/child-a']);
        $parent2->addSubItem('parent2-child-b', 'Child B', ['url' => '/parent-2/child-b']);

        $this->assertTrue($parent1->hasActiveChild());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupRoutes();
    }

    private function setupRoutes()
    {
        \Route::get('/articles/{slug}', function ($slug) {
            return 'Hello, ' . $slug;
        });

        \Route::get('/about', function () {
            return 'About Us';
        });
    }

    /**
     * Returns a request mock with the given path
     * // Even though Taylor states it shouldn't be done, I do
     *
     * @param string $path  Must not contain the trailing slash eg.: "contact"
     *
     * @return \Mockery\MockInterface
     */
    private function mockRequest($path)
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

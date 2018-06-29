<?php
/**
 * Contains the ItemCollectionTest class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-23
 *
 */


namespace Konekt\Menu\Tests\Unit;


use Konekt\Menu\Exceptions\DuplicateItemNameException;
use Konekt\Menu\Menu;
use Konekt\Menu\MenuFactory;
use Konekt\Menu\Tests\TestCase;

class ItemCollectionTest extends TestCase
{
    /** @var  Menu */
    protected $menu;

    public function testMagicWhereMethod()
    {
        $this->assertEquals(5, $this->menu->items->whereCategory('internal')->count());
        $this->assertEquals(1, $this->menu->items->whereEngine('google')->count());

        // Should be no active item, since menu was created with auto_activate off
        $this->assertEquals(0, $this->menu->items->whereClass('active')->count());

        $this->menu->home->activate(); // Activate the 'home' item
        // Now we should be able to search by class as well
        $this->assertEquals(1, $this->menu->items->whereClass('active')->count());

        // Keys of returned items should be their names
        foreach ($this->menu->items->whereCategory('internal') as $key => $item) {
            $this->assertEquals($key, $item->name);
        }
    }

    public function testChildrenRetrieval()
    {
        $this->assertEquals(2, $this->menu->about->children()->count());
    }

    public function testDuplicatesAreNishNish()
    {
        $this->expectException(DuplicateItemNameException::class);
        $this->menu->addItem('about', 'About Duplicate');
    }

    public function testRoots()
    {
        $this->assertCount(4, $this->menu->items->roots());
        $this->menu->items->remove('home');
        $this->assertCount(3, $this->menu->items->roots());
    }

    public function testHaveChild()
    {
        $this->assertCount(1, $this->menu->items->haveChild());

        $this->menu->contact->addSubItem('a', 'A', '/a');

        $this->assertCount(2, $this->menu->items->haveChild());
    }

    public function testHaveParent()
    {
        $this->assertCount(2, $this->menu->items->haveParent());

        $this->menu->contact->addSubItem('b', 'B', '/b');

        $this->assertCount(3, $this->menu->items->haveParent());
    }

    public function testItemsCanBeRemoved()
    {
        $originalCount = $this->menu->items->count();
        $this->assertTrue($this->menu->items->has('home'));

        $this->menu->removeItem('home');

        $this->assertFalse($this->menu->items->has('home'));
        $this->assertEquals($originalCount - 1, $this->menu->items->count());
    }

    public function testItemsCanBeRemovedAlongWithChildren()
    {
        // Add a sub-sub item so that we can check if it actually removes them all
        $this->menu->getItem('about-us')->addSubItem('about-us-team', 'About Us - The Team', ['url' => '/about/us/team']);
        $originalCount = $this->menu->items->count();

        $this->menu->removeItem('about');

        $this->assertFalse($this->menu->items->has('about'));
        $this->assertFalse($this->menu->items->has('about-us'));
        $this->assertFalse($this->menu->items->has('about-our-product'));

        $this->assertEquals($originalCount - 4, $this->menu->items->count());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->menu = MenuFactory::create('sidebar', ['auto_activate' => false]);
        $this->menu->addItem('home', 'Home', ['url' => '/', 'category' => 'internal']);

        $this->menu->addItem('about', 'About', ['url' => '/about', 'category' => 'internal']);
        $this->menu->about->addSubItem('about-us', 'About Us', ['url' => '/about/us', 'category' => 'internal']);
        $this->menu->about->addSubItem('about-our-product', 'About Our Product', ['url' => '/about/our-product', 'category' => 'internal']);

        $this->menu->addItem('contact', 'Contact', ['url' => '/contact', 'category' => 'internal']);
        $this->menu->addItem('google', 'Google', 'https://google.com')->data('engine', 'google');
    }

}

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
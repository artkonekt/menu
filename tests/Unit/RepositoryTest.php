<?php
/**
 * Contains the SmokeTest class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-16
 *
 */


namespace Konekt\Menu\Tests\Unit;


use Konekt\Menu\Exceptions\MenuAlreadyExistsException;
use Konekt\Menu\Repository;
use Konekt\Menu\Tests\TestCase;

class RepositoryTest extends TestCase
{
    /** @var  Repository */
    protected $repo;

    public function setUp()
    {
        parent::setUp();

        $this->repo = $this->app->make('menu');
    }

    public function testEmptyMenuCanBeCreated()
    {
        $sidebar = $this->repo->create('sidebar');

        $this->assertNotNull($sidebar);
        $this->assertCount(0, $sidebar->items);
    }

    public function testMenuItemsCanBeCreated()
    {
        $sidebar = $this->repo->create('sidebar');
        $sidebar->addItem('home', 'Home');
        $sidebar->addItem('somewhere-else', 'Somewhere else');

        $this->assertCount(2, $sidebar->items);
    }

    public function testMenuCanBeRetrievedByKey()
    {
        $menu = $this->repo->create('sidebar');
        $menu->addItem('moscow', 'Moscow');
        $menu->addItem('tallin', 'Tallin');
        $menu->addItem('goteborg', 'Göteborg');

        $sidebar = $this->repo->get('sidebar');
        $this->assertNotNull($sidebar);
        $this->assertCount(3, $sidebar->items);

        // Test if facade also gives the same one
        $this->assertEquals($sidebar, \Menu::get('sidebar'));
    }

    public function testNonExistentEntryReturnsNull()
    {
        $this->assertNull($this->repo->get('I do not exist'));
    }

    public function testMenusWithSameNameCantBeCreated()
    {
        $this->repo->create('i_am_unique');
        $this->expectException(MenuAlreadyExistsException::class);
        $this->repo->create('i_am_unique');
    }

    public function testAllActuallyReturnsAll()
    {
        $this->repo->create('splash');
        $this->repo->create('bang');
        $this->repo->create('pow');
        $this->repo->create('phitang');

        $this->assertCount(4, $this->repo->all());

        $this->assertArrayHasKey('splash', $this->repo->all()->all());
        $this->assertArrayHasKey('bang', $this->repo->all()->all());
        $this->assertArrayHasKey('pow', $this->repo->all()->all());
        $this->assertArrayHasKey('phitang', $this->repo->all()->all());
    }

    public function testCanShareMenuToViews()
    {
        $footer = $this->repo->create('footer', ['share' => true]);
        $this->assertEquals($footer, \View::shared('footer'));

        $header = $this->repo->create('header', ['share' => 'nav']);
        $this->assertEquals($header, \View::shared('nav'));
    }

    public function testDoesntShareMenuToViewsByDefault()
    {
        $this->repo->create('sidebar');
        $this->assertNull(\View::shared('sidebar'));

        $this->repo->create('xmenu', ['share' => false]);
        $this->assertNull(\View::shared('xmenu'));
    }

}
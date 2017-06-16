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

        $this->repo = new Repository();
    }

    public function testEmptyMenuCanBeCreated()
    {
        $sidebar = $this->repo->create('sidebar');

        $this->assertNotNull($sidebar);
        $this->assertCount(0, $sidebar->items);
    }

    public function testMenuCanBeCreatedWithItemsInClosure()
    {
        $sidebar = $this->repo->create('sidebar', function ($menu) {
            $menu->add('Home', 'home');
            $menu->add('Somewhere else', 'somewhere-else');
        });

        $this->assertCount(2, $sidebar->items);
    }

    public function testMenuCanBeRetrievedByKey()
    {
        $this->repo->create('sidebar', function ($menu) {
            $menu->add('Moscow', 'moscow');
            $menu->add('Tallin', 'tallin');
            $menu->add('Göteborg', 'goteborg');
        });

        $sidebar = $this->repo->get('sidebar');
        $this->assertNotNull($sidebar);
        $this->assertCount(3, $sidebar->items);
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

}
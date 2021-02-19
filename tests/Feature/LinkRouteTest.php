<?php
/**
 * Contains the LinkRoutesTest class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-22
 *
 */

namespace Konekt\Menu\Tests\Feature;

use Konekt\Menu\Link;
use Konekt\Menu\Tests\TestCase;
use Route;

class LinkRouteTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::resource('user', 'UserController');
        Route::get('/news/read?id={id}', ['as' => 'read_news', 'uses' => 'NewsController@read']);
    }

    /**
     * @dataProvider routesResolverProvider
     */
    public function testRoutesAreResolvedProperly($route, $expectedUrl)
    {
        $link = new Link(['route' => $route]);
        $this->assertEquals($expectedUrl, $link->url());
    }

    public function routesResolverProvider()
    {
        return [
            ['user.index', self::APP_URL . '/user'],
            ['user.store', self::APP_URL . '/user'],
            ['user.create', self::APP_URL . '/user/create'],
            [['user.show', 'user' => 1], self::APP_URL . '/user/1'],
            [['user.edit', 'user' => 2], self::APP_URL . '/user/2/edit'],
            [['user.update', 'user' => 'slug'], self::APP_URL . '/user/slug'],
            [['user.destroy', 'user' => 'john.smith'], self::APP_URL . '/user/john.smith'],
            [['read_news', 'id' => '1023'], self::APP_URL . '/news/read?id=1023'],
        ];
    }
}

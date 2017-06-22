<?php
/**
 * Contains the LinkActionTest class.
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

class LinkActionTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Route::resource('project', 'ProjectController');
        Route::get('/category/{slug}', 'CategoryController@show');
    }

    /**
     * @dataProvider actionsResolverProvider
     */
    public function testActionsAreResolvedProperly($action, $expectedUrl)
    {
        $link = new Link(['action' => $action]);
        $this->assertEquals($expectedUrl, $link->url());
    }

    public function actionsResolverProvider()
    {
        return [
            ['ProjectController@index', self::APP_URL . '/project'],
            ['ProjectController@store', self::APP_URL . '/project'],
            ['ProjectController@create', self::APP_URL . '/project/create'],
            [['ProjectController@show', 'id' => 1], self::APP_URL . '/project/1'],
            [['ProjectController@edit', 'id' => 2], self::APP_URL . '/project/2/edit'],
            [['ProjectController@update', 'id' => 'jira'], self::APP_URL . '/project/jira'],
            [['ProjectController@destroy', 'id' => 'batagang'], self::APP_URL . '/project/batagang'],
            [['CategoryController@show', 'slug' => 'tablets'], self::APP_URL . '/category/tablets'],
        ];

    }


}
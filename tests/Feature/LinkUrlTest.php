<?php
/**
 * Contains the LinkUrlTest class.
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

class LinkUrlTest extends TestCase
{
    /**
     * @dataProvider urlsResolverProvider
     */
    public function testUrlsAreResolvedProperly($url, $expected)
    {
        $link = new Link(['url' => $url]);
        $this->assertEquals($expected, $link->url());
    }

    public function urlsResolverProvider()
    {
        return [
            ['https://zeit.co', 'https://zeit.co'],
            ['http://ft.com', 'http://ft.com'],
            ['//techsylvania.co', '//techsylvania.co'],
            ['https://vuejs.org/v2/guide/#Getting-Started', 'https://vuejs.org/v2/guide/#Getting-Started'],
            ['/about', self::APP_URL . '/about'],
            ['page?id=53', self::APP_URL . '/page?id=53']
        ];
    }
}

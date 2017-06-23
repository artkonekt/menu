<?php
/**
 * Contains the UtilsTest class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-22
 *
 */


namespace Konekt\Menu\Tests\Unit;


use Konekt\Menu\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    /**
     * @dataProvider addHtmlClassProvider
     */
    public function testAddHtmlClass($existing, $newClass, $expected)
    {
        $this->assertEquals($expected, Utils::addHtmlClass($existing, $newClass));
    }

    public function addHtmlClassProvider()
    {
        return [
            ['', 'active', 'active'],
            ['nav-link', 'active', 'nav-link active'],
            ['active', 'active', 'active'],
            ['active active', 'active', 'active'],
            ['nav-link active', 'active', 'nav-link active'],
            [' nav-link  ', 'active ', 'nav-link active'],
            ['','',''],
            ['active ','','active'],
            ['primary col-md-6 primary is-active', 'is-active','primary col-md-6 is-active'],
        ];
    }

    /**
     * @dataProvider isAbsoluteUrlProvider
     */
    public function testIsAbsoulteUrl($url, $expected)
    {
        $this->assertEquals($expected, Utils::isAbsoluteUrl($url),
            sprintf('%s should%s be interpreted as an absoulte URL', $url, $expected ? '' : ' not')
        );
    }

    public function isAbsoluteUrlProvider()
    {
        return [
            ['http://google.com', true],
            ['https://google.com', true],
            ['https://vanilo.io/#/about', true],
            ['https://artkonekt.com?depeche=mode', true],
            ['//laravel.io', true],
            ['//laravel.io/asd/qwe', true],
            ['//symfony.com/?search=kernel&limit=3', true],
            ['//plesk-host:8443/login', true],
            ['/about', false],
            ['news', false],
            ['news?id=27', false],
            ['/news?id=27', false],
            ['/#/vue', false],
            ['?lang=ruby&fw=rails', false]
        ];
    }

    /**
     * @dataProvider attrsToHtmlProvider
     */
    public function testAttrsToHtml($attrs, $expectedHtml)
    {
        $this->assertEquals($expectedHtml, Utils::attrsToHtml($attrs));
    }

    public function attrsToHtmlProvider()
    {
        return [
            [['disabled', 'readonly'], ' disabled readonly'],
            [['disabled' => 'disabled', 'readonly'], ' disabled="disabled" readonly'],
            [['disabled' => 'disabled', 'readonly' => 1], ' disabled="disabled" readonly="1"'],
            [['class' => 'btn btn-primary', 'disabled'], ' class="btn btn-primary" disabled']
        ];
    }

}
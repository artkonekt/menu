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


use Konekt\Menu\Builder;
use PHPUnit\Framework\TestCase;

class SmokeTest extends TestCase
{
    public function testBuilderCanBeCreated()
    {
        $cfg = include __DIR__ . '/../../config/settings.php';
        $menu = new Builder('menu', $cfg['default']);

        $this->assertNotNull($menu);
    }

}
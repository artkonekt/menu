<?php
/**
 * Contains the MenuConfigurationTest class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-22
 *
 */


namespace Konekt\Menu\Tests\Unit;


use Konekt\Menu\Exceptions\InvalidMenuConfigurationException;
use Konekt\Menu\MenuConfiguration;
use Konekt\Menu\Tests\TestCase;

class MenuConfigurationTest extends TestCase
{
    public function testCanBeCreatedWithEmptyOptions()
    {
        $config = new MenuConfiguration();
        $this->assertInstanceOf(MenuConfiguration::class, $config);
    }

    public function testActiveItemSettingOnlyAcceptsProperValues()
    {
        $this->expectException(InvalidMenuConfigurationException::class);
        $config = new MenuConfiguration(['active_element' => 'container']);
    }

    public function testConfigurationDefaultValues()
    {
        $config = new MenuConfiguration();

        $this->assertTrue($config->autoActivate);
        $this->assertTrue($config->activateParents);
        $this->assertTrue($config->cascadeData);
        $this->assertEquals('item', $config->activeElement);
        $this->assertEquals('active', $config->activeClass);
    }

    public function testValuesCanBeSetProperlyFromOptionsArray()
    {
        $config = new MenuConfiguration([
            'auto_activate'    => false,
            'activate_parents' => false,
            'cascade_data'     => false,
            'active_element'   => 'link',
            'active_class'     => 'is-active'
        ]);

        $this->assertFalse($config->autoActivate);
        $this->assertFalse($config->activateParents);
        $this->assertFalse($config->cascadeData);
        $this->assertEquals('link', $config->activeElement);
        $this->assertEquals('is-active', $config->activeClass);

    }

}
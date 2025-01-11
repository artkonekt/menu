<?php

declare(strict_types=1);

namespace Konekt\Menu\Tests\Unit;

use Konekt\Menu\Facades\Menus;
use Konekt\Menu\Tests\TestCase;

class ItemDataTest extends TestCase
{
    public function testDataCanBeWrittenAndReadAsKeyValuePairs()
    {
        $menu = Menus::create('test1');
        $item = $menu->addItem('test1', 'Test1')
            ->withData('layout', 'en');

        $this->assertEquals('en', $item->data->get('layout'));
    }

    public function testMultipleEntriesOfDataCanBeWrittenAtOnceWithAnAssociativeArray()
    {
        $menu = Menus::create('test2');
        $item = $menu->addItem('test2', 'Test2')
            ->pushData(['icon' => 'calendar', 'accent' => true]);

        $this->assertEquals('calendar', $item->data->get('icon'));
        $this->assertTrue($item->data->get('accent'));
    }

    public function testAllDataEntriesCanBeRetrieved()
    {
        $menu = Menus::create('test3');
        $item = $menu->addItem('test3', 'Test3')
            ->withData('layout', 'en')
            ->pushData(['icon' => 'calendar', 'accent' => true]);

        $data = $item->data->all();

        $this->assertCount(3, $data);
        $this->assertEquals('calendar', $data['icon']);
        $this->assertEquals('en', $data['layout']);
        $this->assertTrue($data['accent']);
    }
}

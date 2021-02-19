<?php
/**
 * Contains the CustomRendererTest class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-27
 *
 */

namespace Konekt\Menu\Tests\Feature;

use Illuminate\Support\Str;
use Konekt\Menu\Tests\Feature\Renderer\BulmaMenuRenderer;
use Konekt\Menu\Tests\TestCase;
use Menu;

class CustomRendererTest extends TestCase
{
    public function testBulmaRendererExample()
    {
        $this->app->singleton('konekt.menu.renderer.menu.bulma', BulmaMenuRenderer::class);

        $menu = Menu::create('bulma', [
            'active_element' => 'link',
            'active_class'   => 'is-active'
        ]);

        $menu->addItem('dashboard', 'Dashboard', '/dashboard');
        $menu->addItem('customers', 'Customers', '/customers');
        $menu->addItem('team', 'Team', '#')->activate();
        $menu->team->addSubItem('members', 'Members', '/team/members');
        $menu->team->addSubItem('plugins', 'Plugins', '/team/plugins');
        $menu->team->plugins->addSubItem('addNewPlugin', 'Add New Plugin', '/team/plugins/new');

        $html = $menu->render('bulma');

        $this->assertStringContainsString('<aside class="menu">', $html);

        $this->assertEquals(1, substr_count($html, '<aside class="menu">'));
        $this->assertEquals(1, substr_count($html, '</aside>'));
        $this->assertEquals(1, substr_count($html, '<ul class="menu-list">'));
        $this->assertEquals(3, substr_count($html, '</ul>'));
        $this->assertEquals($menu->items->count(), substr_count($html, '<li'));
        $this->assertEquals($menu->items->count(), substr_count($html, '</li>'));
        $this->assertEquals($menu->items->count(), substr_count($html, '<a '));
        $this->assertEquals($menu->items->count(), substr_count($html, '</a>'));

        $this->assertContainsLink('dashboard', $html, 1);
        $this->assertContainsLink('customers', $html, 1);
        $this->assertContainsLink('team/members', $html, 1);
        $this->assertContainsLink('team/plugins', $html, 1);
        $this->assertContainsLink('team/plugins/new', $html, 1);
        $this->assertContainsLink('#', $html, 1);
    }

    protected function assertContainsLink($link, $html, $times = null)
    {
        if (Str::startsWith($link, '#')) {
            $url = $link;
        } elseif (Str::startsWith($link, '/')) {
            $url = self::APP_URL . $link;
        } else {
            $url = self::APP_URL . '/' . $link;
        }

        $needle = sprintf('<a href="%s"', $url);
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString($needle, $html);
        } else {
            $this->assertContains($needle, $html);
        }

        if (!is_null($times)) {
            $this->assertEquals($times, substr_count($html, $needle));
        }
    }
}

<?php
/**
 * Contains the BulmaMenuRenderer.php class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-27
 *
 */


namespace Konekt\Menu\Tests\Feature\Renderer;


use Konekt\Menu\Contracts\MenuRenderer;
use Konekt\Menu\Item;
use Konekt\Menu\ItemCollection;
use Konekt\Menu\Menu;

class BulmaMenuRenderer implements MenuRenderer
{
    public function render(Menu $menu)
    {
        $result = sprintf("<aside%s class=\"menu\">\n", $menu->attributesAsHtml());
        $result .= $this->renderLevel($menu->items->roots(), 1);
        $result .= "</aside>\n";

        return $result;
    }

    protected function renderLevel(ItemCollection $items, $level)
    {
        $tabs  = str_repeat("\t", $level);
        $class = $level == 1 ? ' class="menu-list"' : '';

        $result = "$tabs<ul$class>\n";
        foreach ($items as $item) {
            $result .= $this->renderItem($item, $level);
        }

        return $result . "$tabs</ul>\n";
    }

    protected function renderItem(Item $item, $level)
    {
        if ($item->hasChildren()) {
            return $this->renderItemLi($item, $level,
                $this->renderLevel($item->children(), $level + 1)
            );
        }

        return $this->renderItemLi($item, $level);
    }

    protected function renderItemLi(Item $item, $level, $extraHtml = '')
    {
        $tabs = str_repeat("\t", $level + 1);
        $link = sprintf('<a href="%s"%s>%s</a>',
            $item->link->url(),
            $item->link->attributesAsHtml(),
            $item->title
        );

        if (empty($extraHtml)) {
            return sprintf("%s<li%s>%s</li>\n", $tabs, $item->attributesAsHtml(), $link);
        }

        return sprintf("%s<li%s>\n%s%s\n%s\n%s</li>\n",
            $tabs,
            $item->attributesAsHtml(),
            $tabs,
            $link,
            $extraHtml,
            $tabs
        );
    }


}
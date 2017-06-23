<?php
/**
 * Contains the UlMenuRenderer class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-23
 *
 */


namespace Konekt\Menu\Renderers;


use Konekt\Menu\Contracts\MenuRenderer;
use Konekt\Menu\Menu;

class UlMenuRenderer implements MenuRenderer
{
    public function render(Menu $menu)
    {
        $result = sprintf("<ul%s>\n", $menu->attributesAsHtml());

        foreach ($menu->items as $item) {
            $result .= "\t" . $item->render($item->renderer ?: 'li') . "\n";
        }
        $result .= "</ul>\n";

        return $result;
    }


}
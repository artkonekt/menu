<?php

declare(strict_types=1);
/**
 * Contains the DivMenuRenderer class.
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

class DivMenuRenderer implements MenuRenderer
{
    public function render(Menu $menu)
    {
        $result = sprintf("<div%s>\n", $menu->attributesAsHtml());

        foreach ($menu->items as $item) {
            $result .= "\t" . $item->render($item->renderer ?: 'div') . "\n";
        }
        $result .= "</div>\n";

        return $result;
    }
}

<?php
/**
 * Contains the LiItemRenderer class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-23
 *
 */


namespace Konekt\Menu\Renderers;


use Konekt\Menu\Contracts\ItemRenderer;
use Konekt\Menu\Item;

class LiItemRenderer implements ItemRenderer
{
    public function render(Item $item)
    {
        if($item->hasLink()) {
            $link = sprintf('<a href="%s"%s>%s</a>',
                $item->link->url(),
                $item->link->attributesAsHtml(),
                $item->title
            );
        } else {
            $link = $item->title;
        }

        return sprintf("<li%s>%s</li>", $item->attributesAsHtml(), $link);
    }

}
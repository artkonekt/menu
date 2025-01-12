<?php

declare(strict_types=1);

use Konekt\Menu\Menu;

if (! function_exists('menu')) {
    function menu(string $name): ?Menu
    {
        return \Konekt\Menu\Facades\Menus::get($name);
    }
}

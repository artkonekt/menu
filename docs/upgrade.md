# Upgrade

## 1.x -> 2.x

The 2.0 version is a major rework of the initial 1.x series of the library.
It comes with many interface and feature changes, adds parameter and return types,
and abandons magic methods and properties in favour of an explicit API.

### Facade Renamed

The `Menu` facade has been renamed to `Menus` in order to avoid ambiguity with the `Menu` (single menu instance) class.

```php
use Konekt\Menu\Facades\Menu; // <- v1.x

use Konekt\Menu\Facades\Menus; // <- v2.x
```

#### Why?

The `Menus` facade points to the Menu repository, and it can be used to create and retrieve menus.
Additionally, v1.x contained three different `Menu` classes:

1. The `Konekt\Menu\Facades\Menu` Facade, which pointed to a singleton instance of the menu `Repository` class.
2. The `\Menu` class which is a [Laravel alias](https://laravel.com/docs/11.x/packages#package-discovery), and it pointed to the `Menu` Facade.
3. The `Konekt\Menu\Menu` class which represents a single menu.

With v2, this has been changed to:

1. The facade is now `Menus` (within the same namespace)
2. The `\Menu` alias is kept for compatibility, but in views now you should use the `menu()` helper instead: `menu('topmenu')` which is equivalent to `\Menu::get('topmenu')`
3. The `Konekt\Menu\Menu` has not changed.

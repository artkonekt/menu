# Configuration

You can configure the behavior of each menu separately by passing an array of settings when creating a menu:

```php
Menu::create('menu', [
    'auto_activate'    => false,
    'activate_parents' => true,
    'active_class'     => 'active',
    'active_element'   => 'item',    // item|link
    'share'            => 'myMenu'   // Will be available as `$myMenu` in all blade files (uses `View::share()`)
]);
```

## Options

| Option           | Type         | Default    | Meaning                                                                                                                                                             |
|------------------|--------------|------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| auto_activate    | bool         | true       | Menu items with matching pattern (see `activateOnUrls()` will automatically be set as active based on the current requests URI                                      |
| activate_parents | bool         | true       | All the parents of an active item will also be set as active when enabled.                                                                                          |
| active_class     | string       | 'active'   | The CSS class name to apply for active items                                                                                                                        |
| active_element   | string       | 'link'     | [_'link'_ or _'item'_] Determines which HTML element needs to be marked as "active"                                                                                 |
| cascade_data     | bool         | false      | If true, then setting a metadata on an item will automatically be copied to all its children as well                                                                |
| share            | bool\|string | false      | Whether to share with all Blade views as a variable. If true the menu will be shared by its name. If a string is passed, then that will be the name of the variable |

### Sharing Explained

It is possible to automatically make the menu available across all application views by passing the `share` option:

```php
Menu::create('sidebar', ['share' => true]); // will be $sidebar in views
Menu::create('main-menu', ['share' => 'mainMenu']); // will be $mainMenu in views
```

> The underlying code will invoke [Laravel's `View::share()` method](https://laravel.com/docs/11.x/views#sharing-data-with-all-views).

As a result, in a blade view you can:

```blade
{{-- Render with the built in 'ul' renderer --}}
{!! $mainMenu->render('ul') !!}

{{--Or render items manually--}}
<nav>
    @foreach($mainMenu->items as $item)
        <div class="nav-link><a href="{{ $item->url }}">{{ $item->title }}</a></div>
    @endforeach
</nav>
```

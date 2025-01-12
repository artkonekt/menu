# Menus

## Creating Menus

Applications can have one or more menus, either global ones, which are available on every page, or scoped ones, that
are only available in certain areas.

Menus that are available on every page of the application/website are best to be created in the
`AppServiceProvider::boot()` method, so any request hits your application, the menu will be available.

Menus that are available only to certain pages of an app/website can also be created within a controller, in
[view composers](https://laravel.com/docs/11.x/views#view-composers), or anywhere else according to your preference.

**Every menu is uniquely identified by its name** like `topmenu`, `sidebar`, or `products-submenu`, etc.

Regardless of the scope and location, menus should be created using the `Menus` facade:

**Example:**

```php
use Konekt\Menu\Facades\Menus;

$sidebar = Menus::create('topmenu');
$sidebar->addItem('customers', 'Customers',  '/customers');
$sidebar->addItem('contracts', 'Contracts', '/contracts');
$sidebar->addItem('invoices', 'Invoices', '/invoices');
```

## Rendering

The easiest way to access the menu in a blade view is to use the `menu()` helper.

**Using a built-in renderer:**

```blade
{{-- Render with the built in 'ul' renderer --}}
{!! menu('sidebar')?->render('ul') !!}
```

**Rendering items manually:**

```blade
@if($sidebar = menu('sidebar'))
  <ul class="sidebar">
  @foreach($sidebar->items as $item)
    <li class="sidebar-item"><a href="{!! $item->url() !!}">{{ $item->title }}</a></li>    
  @endforeach
  </ul>
@endif
```

> Read the [Rendering Page](rendering.md) of this Documentation to learn all the rendering features available.



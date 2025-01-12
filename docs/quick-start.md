# Quick Start

Navigation menus are essential parts of applications. This library offers PHP developers tools to create, organize,
and manage menus using PHP classes.

An application can have one or more menus, each identified by name like `main`, `sidebar`, or `account-menu`, etc.

## Define Menus

Menus are typically defined in the `AppServiceProvider::boot()` method, so any request hits your application, the menu
objects will be available.

**Example:**

```php
namespace App\Providers

use Illuminate\Support\ServiceProvider;
use Konekt\Menu\Facades\Menus;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $sidebar = Menus::create('sidebar');
        $sidebar->addItem('home', 'Home',  '/');
        $sidebar->addItem('about', 'About', '/about');
    }
}
```

### Rendering

This library focuses on the backend, PHP part of menu building, including the HTML generation.
The frontend framework, and the menu styling is up to the application, however there are several out-of-the box
renderers that can be used as a base:

- `ul` + `li` renderer,
- `ol` + `li` renderer, and
- `div`-based renderer.

```php
Menu::get('sidebar')->render('ul');
//= """
//  <ul>\n
//  \t<li class="active"><a href="http://localhost:8080">Home</a></li>\n
//  \t<li><a href="http://localhost:8080/about">About</a></li>\n
//  </ul>\n
```

Besides these, you can also simply use the menus in Blade templates and render them using foreach loops:

```blade
<div class="menu">
{{-- Iterate through the menu items --}}
@foreach($menu->items as $item)
    @if($item->isAllowed())
        <div class="menu-item">
            @if($item->hasLink)
              <a href="{!! $item->url() !!}">{{ $item->title }}</a>
            @else
              {{ $item->title }}
            @endif
        </div>
    @endif
@endforeach
</div>
```

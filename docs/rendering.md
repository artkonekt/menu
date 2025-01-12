# Rendering Menus

To render a menu, you have the following options:

1. Use a [built-in renderer](#built-in-renderers);
2. Write a [custom renderer](#custom-renderers);
3. Rendering [manually in blade](#manual-rendering);

## Built-in Renderers

This library provides 3 rendering methods out of the box, `ul`, `ol` and `div`.

Renderers can be referred to by their names as the first argument of the render method:

```blade
{!! menu('my-menu')?->render('ul') !!}
```

This will render an HTML like this:

```html
<ul>
  <li class="active"><a href="http://yourdomain.com">Home</a></li>
  <li><a href="http://yourdomain.com/about">About</a></li>
  <li><a href="http://yourdomain.com/services">Services</a></li>
  <li><a href="http://yourdomain.com/contact">Contact</a></li>
</ul>
```
### Render As UL

```php
$menu = Menus::create('menu');
$menu->addItem('home', 'Home', 'https://acme.io');
$menu->addItem('about', 'About', 'https://acme.io/about');
```

In a blade template:

```blade
{!! $menu->render('ul') !!}
```

Result:

```html
<ul>
  <li class="active"><a href="https://acme.io">Home</a></li>
  <li><a href="https://acme.io/about">About</a></li>
</ul>
```

### Render As OL

```php
$menu = Menu::create('menu', ['class' => 'navigation', 'auto_activate' => false]);
$menu->addItem('home', 'Home', '/');
$menu->addItem('about', 'About', '/about');
```

Blade Template:

```blade
{!! $menu->render('ol') !!}
```

HTML Result:

```html
<ol class="navigation">
  <li><a href="http://acme.io/">Home</a></li>
  <li><a href="http://acme.io/about">About</a></li>
</ol>
```

### Render As Div

```php
$menu = Menu::create('menu');
$menu->addItem('home', 'Home', '/');
$menu->addItem('about', 'About', '/about')->attr('data-woink', 'kaboom');
```

Blade template:

```blade
{!! $menu->render('div') !!}
```

HTML Result:

```html
<div>
  <div class="active"><a href="http://acme.io">Home</a></div>
  <div data-woink="kaboom"><a href="http://acme.io/about">About</a></div>
</div>
```

## Custom Renderers

It is possible to implement your own rendering logic in a PHP class and use it.

Rendering was designed to be extensible, therefore it is possible to define separate renderers for menus and for items.

### Custom Renderer Example (Bulma)

> See [Bulma Menu Component](http://bulma.io/documentation/components/menu/) for reference on CSS.

**Create A Menu Renderer Class:**

```php
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
```

**Register the renderer:**

```php
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton('konekt.menu.renderer.menu.bulma', BulmaMenuRenderer::class);    
    }
}
```

**Create the menu:**

```php
$menu = Menu::create('topnav', [
    'active_element' => 'link',
    'active_class' => 'is-active'
]);

$menu->addItem('dashboard', 'Dashboard', '/dashboard');
$menu->addItem('customers', 'Customers', '/customers');
$team = $menu->addItem('team', 'Team', '#')->activate();
$team->addSubItem('members', 'Members', '/team/members');
$team->addSubItem('plugins', 'Plugins', '/team/plugins');
$team->getItem('plugins')->addSubItem('addNewPlugin', 'Add New Plugin', '/team/plugins/new');
```

**Render in a blade template:**

```blade
{!! menu('topnav')->render('bulma') !!}
```

**Result:**

```html
<aside class="menu">
    <ul class="menu-list">
        <li><a href="http://menu.test/dashboard">Dashboard</a></li>
        <li><a href="http://menu.test/customers">Customers</a></li>
        <li>
            <a href="#" class="is-active">Team</a>
            <ul>
                <li><a href="http://menu.test/team/members">Members</a></li>
                <li>
                    <a href="http://menu.test/team/plugins">Plugins</a>
                    <ul>
                        <li><a href="http://menu.test/team/plugins/new">Add New Plugin</a></li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>
</aside>
```

## Manual Rendering

If you prefer not to deal with frontend code in PHP classes, then you can simply render the menus in Blade using loops:

```blade
<ul class="sidebar">
  @foreach(menu('sidebar')?->items as $item)
    <li class="sidebar-item"><a href="{!! $item->url() !!}">{{ $item->title }}</a></li>    
  @endforeach
</ul>
```

### HTML Attributes

If you have added html attributes to the items, you can access them as `$item->attributesAsHtml()`.

```php
$menu = Menus::create('top');

$menu->addItem('home', 'Home', '/')
    ->withAttribute('style', 'color: red');
    
$menu->addItem('posts', 'Posts', '/posts')
    ->withAttributes([
        'disabled',
        'title' => 'Log in to see the blogposts',    
    ]);
```

Render the attributes in blade:

```blade
<ul>
@foreach(menu('top')?->items as $item)
  <li {!! $item->attributesAsHtml() !!}>
    <a href="{!! $item->url() !!}">{{ $item->title }}</a>
  </li>
@endforeach
</ul>
```

Resulting HTML:

```html
<ul>
  <li style="color: red">
    <a href="http://localhost/">Home</a>
  </li>
  <li disabled title="Log in to see the blogposts">
    <a href="http://localhost/posts">Posts</a>
  </li>
</ul>
```

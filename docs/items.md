# Menu Items

## Adding Items

To add a menu item, use the `addItem` method.

**Simple Links:**

```php
$navbar = Menus::create('navbar');

// Simple link; to '/' via the URL helper
$navbar->addItem('home', 'Home', '/');

// External link, being used as-is
$navbar->addItem('duckduckgo', 'Search', 'https://duckduckgo.com/');
```

**Laravel Routes:**

```php
// Named route
$navbar->addItem('clients', 'Clients', ['route' => 'client.index']);
// Named route with parameter
$navbar->addItem('my-profile', 'My Profile', ['route' => ['user.show', 'id' => Auth::user()->id]]);
```

**Links to Controller Actions:**

```php
// Refer to an action
$navbar->addItem('projects', 'Projects', ['action' => 'ProjectController@index']);
// Action with parameter
$navbar->addItem('issue7', 'Issue 7', ['action' => ['IssueController@edit', 'id' => 7]]);
```

The `addItem()` method receives 3 parameters:
- the name of the item
- the title of the item
- and options

*options* can be a simple string representing a URL or an associative array of options and HTML attributes which is described below.

### Adding Sub-items

Multi-level menus can be created by adding sub-items to menu items:

```php
$menu = Menus::create('main');

$crm = $menu->addItem('crm', 'CRM')
$crm->addSubItem('prospects', 'Prospects', ['url' => '/crm/prospects']);
$crm->addSubItem('deals', 'Deals', ['url' => '/crm/deals']);
```

In the example above, the `crm` item _doesn't have a link_, and it _has children_, which means that it will **act like a group**.
These conditions need to be taken into account at rendering:

```blade
<div class="menu">
@foreach($menu->items as $item)
    <div class="menu-item">
        @if($item->hasLink)
            <a href="{!! $item->url() !!}">{{ $item->title }}</a>
        @else
            {{ $item->title }}
            @if($item->hasChildren()
                <div class="submenu">
                    @foreach($item->children() as $child)
                         <a href="{!! $child->url() !!}" class="submenu-item">{{ $child->title }}</a>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
@endforeach
</div>
```


## Removing Items

```php
$menu = Menus::create('main');

$menu->addItem('home', 'Home', '/');
$menu->addItem('about', 'About', '/about');
$menu->getItem('about')->addSubItem('about-us', 'About Us', ['url' => '/about/us']);

// This will remove both about and about-us
$menu->removeItem('about');

// To keep children, set the second parameter `$removeChildren` to false:
$menu->removeItem('about', false); // about-us will remain
```

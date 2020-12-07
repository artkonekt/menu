# Laravel Menu

> This is a rework of [Lavary Menu](https://github.com/lavary/laravel-menu)

[![Tests](https://img.shields.io/github/workflow/status/artkonekt/menu/tests/master?style=flat-square)](https://github.com/artkonekt/menu/actions?query=workflow%3Atests)
[![Stable packagist version](https://img.shields.io/packagist/v/konekt/menu.svg?style=flat-square)](https://packagist.org/packages/konekt/menu)
[![Packagist downloads](https://img.shields.io/packagist/dt/konekt/menu.svg?style=flat-square)](https://packagist.org/packages/konekt/menu)
[![StyleCI](https://styleci.io/repos/94574866/shield?branch=master)](https://styleci.io/repos/94574866)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)

A quick and easy way to create menus in [Laravel 6-8](https://laravel.com/)

## Laravel Compatibility

| Laravel | Menu Module |
|:--------|:------------|
| 5.4     | 1.0 - 1.3   |
| 5.5     | 1.0 - 1.7   |
| 5.6     | 1.1 - 1.7   |
| 5.7     | 1.2 - 1.7   |
| 5.8     | 1.3 - 1.7   |
| 6.x     | 1.4+        |
| 7.x     | 1.5+        |
| 8.x     | 1.7+        |

## PHP Compatibility

| PHP | Menu Module |
|:----|:------------|
| 7.0 | 1.0 - 1.2   |
| 7.1 | 1.0 - 1.5   |
| 7.2 | 1.1 - 1.7   |
| 7.3 | 1.3+        |
| 7.4 | 1.5+        |
| 8.0 | 1.8+        |

## Documentation

* [Installation](#installation)
* [Getting Started](#getting-started)
* [Sub-items](#sub-items)
* [Referring to Items](#referring-to-items)
	- [Get All Items](#get-all-items)
	- [Get Sub-items of the Item](#get-sub-items-of-the-item)
	- [Magic Where Methods](#magic-where-methods)
* [Referring to Menu Objects](#referring-to-menu-instances)
* [HTML Attributes](#html-attributes)
* [Manipulating Links](#manipulating-links)
	- [Link's Href Property](#links-href-property)
* [Active Item](#active-item)
	- [URL Wildcards](#url-wildcards)
    - [Check for Active Children](#check-for-active-children) 
* [Inserting a Separator](#inserting-a-separator)
* [Append and Prepend](#append-and-prepend)
* [Meta Data](#meta-data)
* [Manipulating The Items](#manipulating-the-items)
* [Sorting the Items](#sorting-the-items)
* [Rendering](#rendering)
    - [Built-in Renderers](#built-in-renderers)
        - [Render As UL](#render-as-ul)
        - [Render As OL](#render-as-ol)
        - [Render As Div](#render-as-div)
	- [Custom Renderers](#custom-renderers)
* [Authorization](#authorization)
* [Configuration](#configuration)


## Installation

```bash
composer require konekt/menu
```

> This library supports: **Laravel**: 6.x - 8.x, **PHP**: 7.3 - 8.0 

## Getting Started

You can define the menus in a [middleware](http://laravel.com/docs/master/middleware),
or in a service provider's boot method, so any request hits your
application, the menu objects will be available.

### Create A Menu

```php
$sidebar = Menu::create('sidebar');
$sidebar->addItem('Home',  '/');
$sidebar->addItem('About', 'about');
```

You can reference it later as `Menu::get('sidebar')`.

### Access Menu In Views

If you want the make the menu object available across all application views pass the 'share' option for `create()`:

```php
Menu::create('sidebar', null, ['share' => true]); // will be $sidebar in views
Menu::create('main', null, ['share' => 'mainMenu']); // will be $mainMenu in views
```

In a blade view:

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

### Adding Items

```php
$navbar = Menu::create('navbar');

// Simple link; to '/' via the URL helper
$navbar->addItem('home', 'Home', '/');

// Named route
$navbar->addItem('clients', 'Clients', ['route' => 'client.index']);
// Named route with parameter
$navbar->addItem('my-profile', 'My Profile', ['route' => ['user.show', 'id' => Auth::user()->id]]);

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

### Removing Items

```php
$menu = Menu::create('main');

$menu->addItem('home', 'Home', '/');
$menu->addItem('about', 'About', '/about');
$menu->getItem('about')->addSubItem('about-us', 'About Us', ['url' => '/about/us']);

// This will remove both about and about-us
$menu->removeItem('about');

// To keep children, set the second parameter `$removeChildren` to false:
$menu->removeItem('about', false); // about-us will remain
```

### Render The Menu

This component provides 3 rendering methods out of the box, `ul`, `ol` and `div`.
You can read about the details [here](#rendering-methods).

```blade
{!! $myMenu->render('ul') !!}
```

You can also access the menu object via the `Menu` facade:

```blade
{!! Menu::get('navbar')->render('ul') !!}
```

This will render your menu like this:

```html
<ul>
  <li class="active"><a href="http://yourdomain.com">Home</a></li>
  <li><a href="http://yourdomain.com/about">About</a></li>
  <li><a href="http://yourdomain.com/services">Services</a></li>
  <li><a href="http://yourdomain.com/contact">Contact</a></li>
</ul>
```

## Sub-items

Items can have sub-items too: 

```php
$menu = Menu::create('uberGigaMenu')  

$menu->addItem('about', 'About', ['route' => 'page.about']);
// these items will go under Item 'About'
// refer to about as a property of $menu object then call `addItem()` on it
$menu->about->addSubItem('who-we-are', 'Who We are', '/who-we-are');
// or  
$menu->getItem('about')->addSubItem('what-we-do', 'What We Do', '/what-we-do');
// or  
$menu->addItem('our-goals', 'Our Goals',[
            'parent' => 'about',
            'url' => '/our-goals'
        ]);
```

You can also chain the item definitions and go as deep as you wish:

```php
$menu->addItem('about', 'About', '/about')
    ->addSubItem('level2', 'Level 2', '/about/level2')
        ->addSubItem('level3', 'Level 3', '/about/level2/level3')
            ->addSubItem('level4', 'Level 4', '/about/level2/level4');
```  

It is possible to add sub items directly using `parent` attribute:

```php
$menu->addItem('about', 'About');

// You can either set the item object directly as parent:
$menu->addItem('team', 'The Team', ['url' => '/about-the-team', 'parent' => $menu->about]);

// Or just simply the parent item's name:
$menu->addItem('board', 'The Board', ['url' => '/about-the-board', 'parent' => 'about']);
```

## Referring to Items

You can access defined items throughout your code using the methods described below.

#### Get Item By Name

```php
$menu = \Menu::create('menu');
$menu->addItem('contact', 'Contact', '/contact');

// via the getItem method:
$menu->getItem('contact');

// or via magic property accessor:
$menu->contact;
```

You can also store the item variable for further reference:

```php
$about = $menu->addItem('about', 'About', '/about');
$about->addSubItem('who-we-are', 'Who We Are', '/about/who-we-are');
$about->addSubItem('what-we-do', 'What We Do', '/about/what-we-do');
```

#### Get All Items

Menus have an `items` property that is a collection of menu `Item` objects.

```php
$menu->items; // ItemCollection
// or:
\Menu::get('MyNavBar')->items;
```

`ItemCollection` is a slightly extended [Laravel Collection](https://laravel.com/docs/master/collections).

#### Get Sub-Items of the Item

Get the item using the methods described above then call `children()` on it.

To get children of `About` item:

```php
$aboutSubs = $menu->about->children();

// or outside of the builder context
$aboutSubs = Menu::get('MyNavBar')->about->children();

// Or
$aboutSubs = Menu::get('MyNavBar')->getItem('about')->children();
```

`children()` also returns an `ItemCollection`.

To check if an item has any children or not, you can use `hasChildren()`

```php
if( $menu->about->hasChildren() ) {
    // Do something
}

// or outside of the builder context
Menu::get('MyNavBar')->about->hasChildren();

// Or
Menu::get('MyNavBar')->getItem('about')->hasChildren();
```

#### Magic Where Methods

You can also search the items collection by magic where methods.
These methods are consisted of a `where` concatenated with a property (object property or even meta data)

For example to get items with a specific meta data:

```php
$menu->addItem('Home',     '#')->data('color', 'red');
$menu->addItem('About',    '#')->data('color', 'blue');
$menu->addItem('Services', '#')->data('color', 'red');
$menu->addItem('Contact',  '#')->data('color', 'green');

// Fetch all the items with color set to red:
$reds = $menu->whereColor('red');
```

This method returns an `ItemCollection`.

## Referring to Menu Instances

You might encounter situations when you need to refer to menu instances out of the builder context.

To get a specific menu by name:

```php
$menu = Menu::get('MyNavBar');
```

Or to get all menus instances:

```php
$menus = Menu::all();
```

It returns a *Laravel Collection*

## HTML Attributes

Since all menu items would be rendered as HTML entities like list items or divs, you can define as many HTML attributes as you need for each item:


```php
$menu = Menu::create('MyNavBar');

// As you see, you need to pass the second parameter as an associative array:
$menu->addItem('home', 'Home',  ['route' => 'home.page', 'class' => 'navbar navbar-home', 'id' => 'home']);
$menu->addItem('about', 'About', ['route' => 'page.about', 'class' => 'navbar navbar-about dropdown']);
$menu->addItem('services', 'Services', ['action' => 'ServicesController@index']);
$menu->addItem('contact', 'Contact',  'contact');
```

If you render it with the `ul` renderer, the result will be something like this:

```html
<ul>
  <li class="navbar navbar-home" id="home"><a href="http://yourdomain.com">Home</a></li>
  <li class="navbar navbar-about dropdown"><a href="http://yourdomain.com/about">About</a></li>
  <li><a href="http://yourdomain.com/services">Services</a></li>
  <li><a href="http://yourdomain.com/contact">Contact</a></li>
</ul>
```

It is also possible to set or get HTML attributes after the item has been defined using `attr()` method.


- If you call `attr()` with one argument, it will return the attribute value for you.
- If you call it with two arguments, It will consider the first and second parameters as a key/value pair and sets the attribute.
- You can also pass an associative array of attributes if you need to add a group of HTML attributes in one step
- Lastly if you call it without any arguments it will return all the attributes as an array.

```php
$menu->addItem('about', 'About', ['url' => 'about', 'class' => 'about-item']);

echo $menu->about->attr('class');  // output:  about-item

$menu->about->attr('class', 'another-class');
echo $menu->about->attr('class');  // output: another-class

$menu->about->attr(['class' => 'yet-another', 'id' => 'about']); 

echo $menu->about->attr('class');  // output:  yet-another
echo $menu->about->attr('id');  // output:  about

print_r($menu->about->attr());

/* Output
Array
(
    [class] => yet-another
    [id] => about
)
*/
```

You can use `attr` on an ItemCollection, if you need to target a group of items:

```php
$menu->addItem('About', 'about');

$menu->about->addSubItem('whoweare', 'Who we are', 'about/whoweare');
$menu->about->addSubItem('whatwedo', 'What we do', 'about/whatwedo');

// add a class to children of About
$menu->about->children()->attr('class', 'about-item');
```

## Manipulating Links

All the HTML attributes will go to the wrapping tags(li, div, etc); You might encounter situations when you need to add some HTML attributes to `<a>` tags as well.

Each `Item` instance has an attribute which stores an instance of `Link` object. This object is provided for you to manipulate `<a>` tags.

Just like each item, `Link` also has an `attr()` method which functions exactly like item's:

```php
$menu = Menu::create('MyNavBar');

$about = $menu->addItem('About', ['route' => 'page.about', 'class' => 'navbar navbar-about dropdown']);

$about->link->attr('data-toggle', 'dropdown');
```

#### Link's Href Property

If you don't want to use the routing feature or you don't want the builder to prefix your URL with anything (your host address for example), you can explicitly set your link's href property:

```php
$menu->addItem('about', 'About')->link->href('#');
```

## Active Item

You can mark an item as activated using `activate()` on that item:

```php
$menu->addItem('home', 'Home', '/')->activate();
/* Output
<li class="active"><a href="/">Home</a></li>	
*/	
```

You can also add class `active` to the anchor element instead of the wrapping element (`div` or `li`):

```php
$menu->addItem('home', 'Home', '/')->link->active();
	
/* Output
<li><a class="active" href="/">Home</a></li>	
*/
```

The Menu component automatically activates the corresponding item based
on the current **URI** the time you register the item.

You can disable auto activation or choose the element to be activated (item or the link):

```php
// To prevent from auto activation
Menu::create('nav', [
    'auto_activate' => false
]);
// To set the active element:
Menu::create('nav', [
    'active_element' => 'link'    // item|link - item by default
]);
```

#### URL Wildcards

Konekt Menu component makes you able to define a pattern for a certain item, if the automatic activation can't help:

```php
$menu->addItem('articles', 'Articles', '/articles')->activateOnUrls('articles/*');
```

So `articles`, `articles/random-news-title` will both activate the `Articles` item.

### Check for Active Children

You can check if a menu item has an active sub item by calling:

```php
$item->hasActiveChild();
// (bool) false
```

You can get the active item(s) from an item colletion by applying the `actives()` filter on them:

```php
$menu->roots()->actives();
// Konekt\Menu\ItemCollection

// or:

$item->children()->actives();
// Konekt\Menu\ItemCollection
```

## Append and Prepend


You can `append` or `prepend` HTML or plain-text to each item's title after it is defined:

```php
<?php
$menu = Menu::create('MyNavBar');

$about = $menu->addItem('about', 'About', ['route'  => 'page.about', 'class' => 'navbar navbar-about dropdown']);
$menu->about->attr(['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown'])
          ->append(' <b class="caret"></b>')
          ->prepend('<span class="glyphicon glyphicon-user"></span> ');
```

The above code will result:

```html
<ul>
  <li class="navbar navbar-about dropdown">
   <a href="about" class="dropdown-toggle" data-toggle="dropdown">
     <span class="glyphicon glyphicon-user"></span> About <b class="caret"></b>
   </a>
  </li>
</ul>

```

You can call `prepend` and `append` on item collections as well so that it'll affect all the items.


## Meta Data

You might encounter situations when you need to attach some meta data to each item; This data can be anything from item placement order to permissions required for accessing the item; You can do this by using `data()` method.

`data()` method works exactly like `attr()` method:

If you call `data()` with one argument, it will return the data value for you.
If you call it with two arguments, It will consider the first and second parameters as a key/value pair and sets the data. 
You can also pass an associative array of data if you need to add a group of key/value pairs in one step; Lastly if you call it without any arguments it will return all data as an array.

```php
$menu->addItem('users', 'Users', ['route'  => 'admin.users'])
    ->data('permission', 'manage_users');
```

You can also access a data as if it's a property:

```php
$menu->addItem('users', 'Users', '/users')->data('placement', 12);
echo $menu->users->placement;    // Output : 12
```

Meta data don't do anything to the item and won't be rendered in HTML either. It is the developer who would decide what to do with them.

You can use `data` on a collection, if you need to target a group of items:

```php
$menu->addItem('users', 'Users');
  
$menu->users->addSubItem('create_user', 'New User', ['route' => 'user.create']);
$menu->users->addSubItem('list_users', 'Uses', ['route' => 'user.index']);
  
// add a meta data to children of Users
$menu->users->children()->data('tag', 'admin');
```

## Manipulating The Items

Menu items collection can be filtered, sorted, etc by any of [Illuminate Collection methods](laravel.com/docs/5.4/collections#available-methods)

## Rendering

### Built-in Renderers

Several rendering formats are available out of the box:

1. ul
2. ol
3. div

#### Render As UL

```php
$menu = Menu::create('menu');
$menu->addItem('home', 'Home', '/');
$menu->addItem('about', 'About', '/about');
```

In blade template:
```blade
{!! $menu->render('ul') !!}
```

Result:
```html
<ul>
  <li class="active"><a href="http://acme.io">Home</a></li>
  <li><a href="http://acme.io/about">About</a></li>
</ul>
```

#### Render As OL

```php
$menu = Menu::create('menu', ['class' => 'navigation', 'auto_activate' => false]);
$menu->addItem('home', 'Home', '/');
$menu->addItem('about', 'About', '/about');
```

Template:
```blade
{!! $menu->render('ol') !!}
```

Result:
```html
<ol class="navigation">
  <li><a href="http://acme.io">Home</a></li>
  <li><a href="http://acme.io/about">About</a></li>
</ol>
```

#### Render As Div

```php
$menu = Menu::create('menu');
$menu->addItem('home', 'Home', '/');
$menu->addItem('about', 'About', '/about')->attr('data-woink', 'kaboom');
```

In Blade:
```blade
{!! $menu->render('div') !!}
```

Result:
```html
<div>
  <div class="active"><a href="http://acme.io">Home</a></div>
  <div data-woink="kaboom"><a href="http://acme.io/about">About</a></div>
</div>
```

### Custom Renderers

Rendering was designed to be extensible from ground up.

It is possible to define separate renderers for menus and for items.

#### Custom Renderer Example (Bulma)

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
app()->singleton('konekt.menu.renderer.menu.bulma', BulmaMenuRenderer::class);
```
**Create the menu:**
```php
$menu = Menu::create('bulma', [
            'active_element' => 'link',
            'active_class'   => 'is-active',
            'share'          => 'bulmaMenu'
        ]);

$menu->addItem('dashboard', 'Dashboard', '/dashboard');
$menu->addItem('customers', 'Customers', '/customers');
$menu->addItem('team', 'Team', '#')->activate();
$menu->team->addSubItem('members', 'Members', '/team/members');
$menu->team->addSubItem('plugins', 'Plugins', '/team/plugins');
$menu->team->plugins->addSubItem('addNewPlugin', 'Add New Plugin', '/team/plugins/new');
```

**Render in blade template:**
```blade
{!! $bulmaMenu->render('bulma') !!}
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

## Authorization

Items can be authorized for users in two simple ways:

### 1. Checking Via Actions

Based on [Laravel's built-in](https://laravel.com/docs/5.6/authorization#authorizing-actions-using-policies)
authorization, you can pass action names (strings) that will be tested against the current user with
the `can()` method:

```php
$menu = Menu::create('nav', []);
$menu->addItem('users', __('Users'), ['route' => 'app.user.index'])
    ->allowIfUserCan('list users');

$menu->addItem('settings', __('Settings'), ['route' => 'app.settings.index'])
    ->allowIfUserCan('list settings');
```

### 2. Checking Via Callbacks

You can also pass callbacks to authorize menu items for users:

```php
$menu = Menu::create('nav', []);
$menu->addItem('users', __('Users'), ['route' => 'app.user.index'])
    ->allowIf(function($user) {
        return $user->id > 500; // Add your arbitrary condition
    });
```

The callback will receive the user as the first parameter.

> You can add multiple `allowIf` and/or `allowIfUserCan` conditions to an item.
> The item will be allowed if **ALL** the conditions will be met.

### Checking Authorization

> By default, items without any __allow*__ condition are allowed.

**To check if an item is allowed:**

```php
$menu->users->isAllowed(); // Checks if the item users item is allowed for the current user
$menu->settings->isAllowed(\App\User::find(501)); // Check if an item is available for a given user
```

**To get the list of allowed children:**

```php
$item->childrenAllowed(); // Returns an ItemCollection of the allowed item for the current user
$item->childrenAllowed(\App\User::find(123)); // Returns the allowed items for the given user
```

## Configuration

You can adjust the behavior of the menu builder by passing settings when creating a menu:

* **auto_activate** Automatically activates menu items based on the current URI (_true_ by default)
* **activate_parents** Activates the parents of an active item (_true_ by default)
* **active_class** CSS class name for active items (_"active"_ by default)
* **cascade_data** If you need descendants of an item to inherit meta data from their parents, make sure this option is enabled (_true_ by default)
* **active_element** Which HTML element to be set active _'link'_ (`<a>`) or _'item'_ (`<li>`, `<div>`, etc) (_item_ by default)

```php
Menu::create('menu', [
    'auto_activate'    => false,
    'activate_parents' => true,
    'active_class'     => 'active',
    'active_element'   => 'item',    // item|link
    'restful'          => true,
    'share'            => 'myMenu'   // Will be available as `$myMenu` in blade files
]);

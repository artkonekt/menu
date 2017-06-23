# Laravel Menu

> This is a rework of [Lavary Menu](https://github.com/lavary/laravel-menu)

[![Latest Stable Version](https://poser.pugx.org/konekt/menu/version.png)](https://packagist.org/packages/konekt/menu)
[![Latest Unstable Version](https://poser.pugx.org/konekt/menu/v/unstable.svg)](https://packagist.org/packages/konekt/menu)
[![Total Downloads](https://poser.pugx.org/konekt/menu/downloads.png)](https://packagist.org/packages/konekt/menu)
[![License](https://poser.pugx.org/konekt/menu/license.svg)](https://packagist.org/packages/konekt/menu)


A quick and easy way to create menus in [Laravel 5](https://laravel.com/)

## Documentation

* [Installation](#installation)
* [Getting Started](#getting-started)
* [Routing](#routing)
	- [URLs](#urls)	
	- [Named Routes](#named-routes)
	- [Controller Actions](#controller-actions)
	- [HTTPS](#https)
* [Sub-items](#sub-items)
* [Set Item's ID Manualy](#)
* [Set Item's Nicknames Manualy](#)
* [Referring to Items](#referring-to-items)
	- [Get Item by Title](#get-item-by-title)
	- [Get Item by Id](#get-item-by-id)
	- [Get All Items](#get-all-items)
	- [Get the First Item](#get-the-first-item)
	- [Get the Last Item](#get-the-last-item)
	- [Get Sub-items of the Item](#get-sub-items-of-the-item)
	- [Magic Where Methods](#magic-where-methods)
* [Referring to Menu Objects](#referring-to-menu-instances)
* [HTML Attributes](#html-attributes)
* [Manipulating Links](#manipulating-links)
	- [Link's Href Property](#links-href-property)
* [Active Item](#active-item)
	- [RESTful URLs](#restful-urls)
	- [URL Wildcards](#url-wildcards)
* [Inserting a Separator](#inserting-a-separator)
* [Append and Prepend](#append-and-prepend)
* [Raw Items](#raw-items)
* [Menu Groups](#menu-groups)
* [URL Prefixing](#url-prefixing)
* [Nested Groups](#nested-groups)
* [Meta Data](#meta-data)
* [Filtering the Items](#filtering-the-items)
* [Sorting the Items](#sorting-the-items)
* [Rendering Methods](#rendering-methods)
	- [Menu as Unordered List](#menu-as-unordered-list)
	- [Menu as Ordered List](#menu-as-ordered-list)
	- [Menu as Div](#menu-as-div)
	- [Menu as Bootstrap 3 Navbar](#menu-as-bootstrap-3-navbar)
* [Advanced Usage](#advanced-usage)
	+ [A Basic Example](#a-basic-example)
	+ [Control Structure for Blade](#control-structure-for-blade)
		- [@lm-attrs](#lm-attrs)
* [Configuration](#configuration)
* [If You Need Help](#if-you-need-help)
* [License](#license)


## Installation

```bash
composer require konekt/menu
```

Now, append Laravel Menu service provider to `providers` array in `config/app.php`:

> *Note*: On Laravel 5.5 and above this step gets automatically done by composer.

```php
//...
'providers' => [
    // Other Service Providers    
    'Konekt\Menu\MenuServiceProvider',
    
    //...
]
?>
```
#### Register The Facade

You can also register the `Menu` facade in `config/app.php`:

```php
'aliases' => [
    // ...
    'Menu' => Konekt\Menu\Facades\Menu::class
]
```


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
$navbar->addItem('Home', '/');

// Named route
$navbar->addItem('Clients', ['route' => 'client.index']);
// Named route with parameter
$navbar->addItem('My Profile', ['route' => ['user.show', 'id' => Auth::user()->id]]);

// Refer to an action
$navbar->addItem('Projects', ['action' => 'ProjectController@index']);
// Action with parameter
$navbar->addItem('Issue 7', ['action' => ['IssueController@edit', 'id' => 7]]);
```

The `addItem()` method receives 3 parameters:
 - the name of the item
 - the title of the item
 - and options

*options* can be a simple string representing a URL or an associative array of options and HTML attributes which is described below.

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
?>
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

>>> BOOKMARK, HERE I LEFT OFF <<<

## Active Item

You can mark an item as activated using `activate()` on that item:

```php
<?php
	// ...
	$menu->addItem('Home', '#')->active();
	// ...
	
	/* Output
	
	<li class="active"><a href="#">#</a></li>	
	
	*/
	
?>
```

You can also add class `active` to the anchor element instead of the wrapping element (`div` or `li`):

```php
<?php
	// ...
	$menu->addItem('Home', '#')->link->active();
	// ...
	
	/* Output
	
	<li><a class="active" href="#">#</a></li>	
	
	*/
	
?>
```

Laravel Menu does this for you automatically according to the current **URI** the time you register the item.

You can also choose the element to be activated (item or the link) in `settings.php` which resides in package's config directory:

```php

	// ...
	'active_element' => 'item',    // item|link
	// ...

```

#### RESTful URLs

RESTful URLs are also supported as long as `restful` option is set as `true` in `config/settings.php` file, E.g. menu item with url `resource` will be activated by `resource/slug` or `resource/slug/edit`.  

You might encounter situations where your app is in a sub directory instead of the root directory or your resources have a common prefix; In such case you need to set `rest_base` option to a proper prefix for a better restful activation support. `rest_base` can take a simple string, array of string or a function call as value.

#### URL Wildcards

`laravel-menu` makes you able to define a pattern for a certain item, if the automatic activation can't help:

```php
<?php
// ...
$menu->addItem('Articles', 'articles')->active('this-is-another-url/*');
// ...
```

So `this-is-another-url`, `this-is-another-url/and-another` will both activate `Articles` item.

## Inserting a Separator

You can insert a separator after each item using `divide()` method:

```php
<?php
	//...
	$menu->addItem('Separated Item', 'item-url')->divide()
	
	// You can also use it this way:
	
	$menu->('Another Separated Item', 'another-item-url');
	
	// This line will insert a divider after the last defined item
	$menu->divide()
	
	//...
	
	/*
	Output as <ul>:
	
		<ul>
			...
			<li><a href="item-url">Separated Item</a></li>
			<li class="divider"></li>
			
			<li><a href="another-item-url">Another Separated Item</a></li>
			<li class="divider"></li>
			...
		</ul>
		
	*/

?>
```

`divide()` also gets an associative array of attributes:

```php
<?php
	//...
	$menu->addItem('Separated Item', 'item-url')->divide( array('class' => 'my-divider') );
	//...
	
	/*
	Output as <ul>:
	
		<ul>
			...
			<li><a href="item-url">Separated Item</a></li>
			<li class="my-divider divider"></li>
		
			...
		</ul>
		
	*/
?>
```


## Append and Prepend


You can `append` or `prepend` HTML or plain-text to each item's title after it is defined:

```php
<?php
Menu::create('MyNavBar', function($menu){

  // ...
  
  $about = $menu->addItem('About',    array('route'  => 'page.about', 'class' => 'navbar navbar-about dropdown'));
  
  $menu->about->attr(array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown'))
              ->append(' <b class="caret"></b>')
              ->prepend('<span class="glyphicon glyphicon-user"></span> ');
              
  // ...            

});
?>
```

The above code will result:

```html
<ul>
  ...
  
  <li class="navbar navbar-about dropdown">
   <a href="about" class="dropdown-toggle" data-toggle="dropdown">
     <span class="glyphicon glyphicon-user"></span> About <b class="caret"></b>
   </a>
  </li>
</ul>

```

You can call `prepend` and `append` on collections as well.

## Raw Items

To insert items as plain text instead of hyper-links you can use `raw()`:

```php
<?php
    // ...
    $menu->raw('Item Title', array('class' => 'some-class'));  
    
    $menu->addItem('About', 'about');
    $menu->About->raw('Another Plain Text Item')
    // ...
    
    /* Output as an unordered list:
       <ul>
            ...
            <li class="some-class">Item's Title</li>
            <li>
                About
                <ul>
                    <li>Another Plain Text Item</li>
                </ul>
            </li>
            ...
        </ul>
    */
?>
```


## Menu Groups

Sometimes you may need to share attributes between a group of items. Instead of specifying the attributes and options for each item, you may use a menu group feature:

**PS:** This feature works exactly like Laravel group routes. 


```php
<?php
Menu::create('MyNavBar', function($menu){

  $menu->addItem('Home',     array('route'  => 'home.page', 'class' => 'navbar navbar-home', 'id' => 'home'));
  
  $menu->group(array('style' => 'padding: 0', 'data-role' => 'navigation') function($m){
    
        $m->addItem('About',    array('route'  => 'page.about', 'class' => 'navbar navbar-about dropdown'));
        $m->addItem('services', array('action' => 'ServicesController@index'));
  }
  
  $menu->addItem('Contact',  'contact');

});
?>
```

Attributes `style` and `data-role` would be applied to both `About` and `Services` items:

```html
<ul>
    <li class="navbar navbar-home" id="home"><a href="http://yourdomain.com">Home</a></li>
    <li style="padding: 0" data-role="navigation" class="navbar navbar-about dropdown"><a href="http://yourdomain.com/about"About</a></li>
    <li style="padding: 0" data-role="navigation"><a href="http://yourdomain.com/services">Services</a></li>
</ul>
```


## URL Prefixing

Just like Laravel route prefixing feature, a group of menu items may be prefixed by using the `prefix` option in the  array being passed to the group.

**Attention:** Prefixing only works on the menu items addressed with `url` but not `route` or `action`. 

```php
<?php
Menu::create('MyNavBar', function($menu){

  $menu->addItem('Home',     array('route'  => 'home.page', 'class' => 'navbar navbar-home', 'id' => 'home'));
  
  $menu->addItem('About', array('url'  => 'about', 'class' => 'navbar navbar-about dropdown'));  // URL: /about 
  
  $menu->group(array('prefix' => 'about'), function($m){
  
  	$about->addItem('Who we are?', 'who-we-are');   // URL: about/who-we-are
  	$about->addItem('What we do?', 'what-we-do');   // URL: about/what-we-do
  	
  });
  
  $menu->addItem('Contact',  'contact');

});
?>
```

This will generate:

```html
<ul>
    <li  class="navbar navbar-home" id="home"><a href="/">Home</a></li>
    
    <li  data-role="navigation" class="navbar navbar-about dropdown"><a href="http://yourdomain.com/about/summary"About</a>
    	<ul>
    	   <li><a href="http://yourdomain.com/about/who-we-are">Who we are?</a></li>
    	   <li><a href="http://yourdomain.com/about/who-we-are">What we do?</a></li>
    	</ul>
    </li>
    
    <li><a href="services">Services</a></li>
    <li><a href="contact">Contact</a></li>
</ul>
```

## Nested Groups

Laravel Menu supports nested grouping feature as well. A menu group merges its own attribute with its parent group then shares them between its wrapped items:

```php
<?php
Menu::create('MyNavBar', function($menu){

	// ...
	
	$menu->group(array('prefix' => 'pages', 'data-info' => 'test'), function($m){
		
		$m->addItem('About', 'about');
		
		$m->group(array('prefix' => 'about', 'data-role' => 'navigation'), function($a){
		
			$a->addItem('Who we are', 'who-we-are?');
			$a->addItem('What we do?', 'what-we-do');
			$a->addItem('Our Goals', 'our-goals');
		});
	});
	
});
?>
```

If we render it as a ul:

```html
<ul>
	...
	<li data-info="test">
		<a href="http://yourdomain.com/pages/about">About</a>
		<ul>
			<li data-info="test" data-role="navigation"><a href="http://yourdomain.com/pages/about/who-we-are"></a></li>
			<li data-info="test" data-role="navigation"><a href="http://yourdomain.com/pages/about/what-we-do"></a></li>
			<li data-info="test" data-role="navigation"><a href="http://yourdomain.com/pages/about/our-goals"></a></li>
		</ul>
	</li>
</ul>
```


## Meta Data

You might encounter situations when you need to attach some meta data to each item; This data can be anything from item placement order to permissions required for accessing the item; You can do this by using `data()` method.

`data()` method works exactly like `attr()` method:

If you call `data()` with one argument, it will return the data value for you.
If you call it with two arguments, It will consider the first and second parameters as a key/value pair and sets the data. 
You can also pass an associative array of data if you need to add a group of key/value pairs in one step; Lastly if you call it without any arguments it will return all data as an array.

```php
<?php
Menu::create('MyNavBar', function($menu){

  // ...
  
  $menu->addItem('Users', array('route'  => 'admin.users'))
       ->data('permission', 'manage_users');

});
?>
```

You can also access a data as if it's a property:

```php
<?php
	
	//...
	
	$menu->addItem('Users', '#')->data('placement', 12);
	
	// you can refer to placement as if it's a public property of the item object
	echo $menu->users->placement;    // Output : 12
	
	//...
?>
```

Meta data don't do anything to the item and won't be rendered in HTML either. It is the developer who would decide what to do with them.

You can use `data` on a collection, if you need to target a group of items:

```php
<?php
  // ...
  $menu->addItem('Users', 'users');
  
  $menu->users->addItem('New User', 'users/new');
  $menu->users->addItem('Uses', 'users');
  
  // add a meta data to children of Users
  $menu->users->children()->data('anything', 'value');
  
  // ...
```

## Filtering the Items

We can filter menu items by a using `filter()` method. 
`Filter()` receives a closure which is defined by you.It then iterates over the items and run your closure on each of them.

You must return false for items you want to exclude and true for those you want to keep.


Let's proceed with a real world scenario:

I suppose your `User` model can check whether the user has an specific permission or not:

```php
<?php
Menu::create('MyNavBar', function($menu){

  // ...
  
  $menu->addItem('Users', array('route'  => 'admin.users'))
       ->data('permission', 'manage_users');

})->filter(function($item){
  if(User::get()->can( $item->data('permission'))) {
      return true;
  }
  return false;
});
?>
```
As you might have noticed we attached the required permission for each item using `data()`.

As result, `Users` item will be visible to those who has the `manage_users` permission.


## Sorting the Items

`laravel-menu` can sort the items based on either a user defined function or a key which can be item properties like id,parent,etc or meta data stored with each item.


To sort the items based on a property and or meta data:

```php
<?php
Menu::create('main', function($m){

	$m->addItem('About', '#')     ->data('order', 2);
	$m->addItem('Home', '#')      ->data('order', 1);
	$m->addItem('Services', '#')  ->data('order', 3);
	$m->addItem('Contact', '#')   ->data('order', 5);
	$m->addItem('Portfolio', '#') ->data('order', 4);

})->sortBy('order');		
?>
```

`sortBy()` also receives a second parameter which specifies the ordering direction: Ascending order(`asc`) and Descending Order(`dsc`). 

Default value is `asc`.


To sort the items based on `Id` in descending order:

```php
<?php
Menu::create('main', function($m){

	$m->addItem('About');
	$m->addItem('Home');
	$m->addItem('Services');
	$m->addItem('Contact');
	$m->addItem('Portfolio');

})->sortBy('id', 'desc');		
?>
```


Sorting the items by passing a closure:

```php
<?php
Menu::create('main', function($m){

	$m->addItem('About')     ->data('order', 2);
	$m->addItem('Home')      ->data('order', 1);
	$m->addItem('Services')  ->data('order', 3);
	$m->addItem('Contact')   ->data('order', 5);
	$m->addItem('Portfolio') ->data('order', 4);

})->sortBy(function($items) {
	// Your sorting algorithm here...
	
});		
?>
```

The closure takes the items collection as argument.


## Rendering Methods

Several rendering formats are available out of the box:

#### Menu as Unordered List

```html
  {!! $MenuName->asUl() !!}
```

`asUl()` will render your menu in an unordered list. it also takes an optional parameter to define attributes for the `<ul>` tag itself:

```php
{!! $MenuName->asUl( array('class' => 'awesome-ul') ) !!}
```

Result:

```html
<ul class="awesome-ul">
  <li><a href="http://yourdomain.com">Home</a></li>
  <li><a href="http://yourdomain.com/about">About</a></li>
  <li><a href="http://yourdomain.com/services">Services</a></li>
  <li><a href="http://yourdomain.com/contact">Contact</a></li>
</ul>
```

#### Menu as Ordered List


```php
  {!! $MenuName->asOl() !!}
```

`asOl()` method will render your menu in an ordered list. it also takes an optional parameter to define attributes for the `<ol>` tag itself:

```php
{!! $MenuName->asOl( array('class' => 'awesome-ol') ) !!}
```

Result:

```html
<ol class="awesome-ol">
  <li><a href="http://yourdomain.com">Home</a></li>
  <li><a href="http://yourdomain.com/about">About</a></li>
  <li><a href="http://yourdomain.com/services">Services</a></li>
  <li><a href="http://yourdomain.com/contact">Contact</a></li>
</ol>
```

#### Menu as Div


```php
  {!! $MenuName->asDiv() !!}
```

`asDiv()` method will render your menu as nested HTML divs. it also takes an optional parameter to define attributes for the parent `<div>` tag itself:

```php
{!! $MenuName->asDiv( array('class' => 'awesome-div') ) !!}
```

Result:

```html
<div class="awesome-div">
  <div><a href="http://yourdomain.com">Home</a></div>
  <div><a href="http://yourdomain.com/about">About</a></div>
  <div><a href="http://yourdomain.com/services">Services</a></div>
  <div><a href="http://yourdomain.com/contact">Contact</a></div>
</div>
```

#### Menu as Bootstrap 3 Navbar

Laravel Menu provides a parital view out of the box which generates menu items in a bootstrap friendly style which you can **include** in your Bootstrap based navigation bars:

You can access the partial view by `config('laravel-menu.views.bootstrap-items')`.

All you need to do is to include the partial view and pass the root level items to it:

```
...

@include(config('laravel-menu.views.bootstrap-items'), array('items' => $mainNav->roots()))

...

```

This is how your Bootstrap code is going to look like:

```html
<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Brand</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">

       @include(config('laravel-menu.views.bootstrap-items'), array('items' => $mainNav->roots()))

      </ul>
      <form class="navbar-form navbar-right" role="search">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>
      <ul class="nav navbar-nav navbar-right">

        @include(config('laravel-menu.views.bootstrap-items'), array('items' => $loginNav->roots()))

      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
```

## Advanced Usage

As noted earlier you can create your own rendering formats.

#### A Basic Example

If you'd like to render your menu(s) according to your own design, you should create two views.

* `View-1`  This view contains all the HTML codes like `nav` or `ul` or `div` tags wrapping your menu items.
* `View-2`  This view is actually a partial view responsible for rendering menu items (it is going to be included in `View-1`.)


The reason we use two view files here is that `View-2` calls itself recursively to render the items to the deepest level required in multi-level menus.

Let's make this easier with an example:

In our `app/Http/routes.php`:

```php
<?php
Menu::create('MyNavBar', function($menu){
  
  $menu->addItem('Home');
  
   $menu->addItem('About',    array('route'  => 'page.about'));
   
   $menu->about->addItem('Who are we?', 'who-we-are');
   $menu->about->addItem('What we do?', 'what-we-do');

  $menu->addItem('services', 'services');
  $menu->addItem('Contact',  'contact');
  
});
?>
```

In this example we name View-1 `custom-menu.blade.php` and View-2 `custom-menu-items.blade.php`.

**custom-menu.blade.php**
```php
<nav class="navbar">
  <ul class="horizontal-navbar">
    @include('custom-menu-items', array('items' => $MyNavBar->roots()))
  </ul>
</nav><!--/nav-->
```

**custom-menu-items.blade.php**
```php
@foreach($items as $item)
  <li @if($item->hasChildren()) class="dropdown" @endif>
      <a href="{!! $item->url() !!}">{!! $item->title !!} </a>
      @if($item->hasChildren())
        <ul class="dropdown-menu">
              @include('custom-menu-items', array('items' => $item->children()))
        </ul> 
      @endif
  </li>
@endforeach
```

Let's describe what we did above, In `custom-menus.blade.php` we put whatever HTML boilerplate code we had according to our design, then we included `custom-menu-items.blade.php` and passed the menu items at *root level* to `custom-menu-items.blade.php`:

```php
...
@include('custom-menu-items', array('items' => $menu->roots()))
...
```

In `custom-menu-items.blade.php` we ran a `foreach` loop and called the file recursively in case the current item had any children.

To put the rendered menu in your application template, you can simply include `custom-menu` view in your master layout.

#### Control Structure For Blade

Laravel menu extends Blade to handle special layouts.

##### @lm-attrs

You might encounter situations when some of your HTML properties are explicitly written inside your view instead of dynamically being defined when adding the item; However you will need to merge these static attributes with your Item's attributes.

```php
@foreach($items as $item)
  <li @if($item->hasChildren()) class="dropdown" @endif data-test="test">
      <a href="{!! $item->url() !!}">{!! $item->title !!} </a>
      @if($item->hasChildren())
        <ul class="dropdown-menu">
              @include('custom-menu-items', array('items' => $item->children()))
        </ul> 
      @endif
  </li>
@endforeach
```

In the above snippet the `li` tag has class `dropdown` and `data-test` property explicitly defined in the view; Laravel Menu provides a control structure which takes care of this.

Suppose the item has also several attributes dynamically defined when being added:

```php
<?php
// ...
$menu->addItem('Dropdown', array('class' => 'item item-1', 'id' => 'my-item'));
// ...
```

The view:

```php
@foreach($items as $item)
  <li@lm-attrs($item) @if($item->hasChildren()) class="dropdown" @endif data-test="test" @lm-endattrs>
      <a href="{!! $item->url !!}">{!! $item->title !!} </a>
      @if($item->hasChildren())
        <ul class="dropdown-menu">
              @include('custom-menu-items', array('items' => $item->children()))
        </ul> 
      @endif
  </li>
@endforeach
```

This control structure automatically merges the static HTML properties with the dynamically defined properties.

Here's the result:

```
...
<li class="item item-1 dropdown" id="my-item" data-test="test">...</li>
...
```


## Configuration

You can adjust the behavior of the menu builder in `config/settings.php` file. Currently it provide a few options out of the box:

* **auto_activate** Automatically activates menu items based on the current URI
* **activate_parents** Activates the parents of an active item
* **active_class** Default CSS class name for active items
* **restful** Activates RESTful URLS. E.g `resource/slug` will activate item with `resource` url.
* **cascade_data** If you need descendants of an item to inherit meta data from their parents, make sure this option is enabled.
* **rest_base** The base URL that all restful resources might be prefixed with.
* **active_element** You can choose the HTML element to which you want to add activation classes (anchor or the wrapping element).

You're also able to override the default settings for each menu. To override settings for menu, just add the lower-cased menu name as a key in the settings array and add the options you need to override:

```php
<?php
return array(
	'default' => array(
		'auto_activate'    => true,
		'activate_parents' => true,
		'active_class'     => 'active',
		'active_element'   => 'item',    // item|link
		'restful'          => true,
	),
	'yourmenuname' => array(
		'auto_activate'    => false
	),
);
```



## If You Need Help

Please submit all issues and questions using GitHub issues and I will try to help you.


## License

*Laravel-Menu* is free software distributed under the terms of the MIT license.

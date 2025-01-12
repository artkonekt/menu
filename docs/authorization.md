# Authorizing Menu Items

You may want to show or hide menu items depending on the authenticated user's permission.

To achieve that, menu items can be authorized for users in two ways:

## 1. Checking Via Actions

Based on [Laravel's built-in](https://laravel.com/docs/11.x/authorization#authorizing-actions-using-policies)
authorization, you can pass action names (strings) that will be tested against the current user with
the `can()` method:

```php
$menu = Menus::create('nav', []);
$menu->addItem('users', __('Users'), ['route' => 'app.user.index'])
    ->allowIfUserCan('list users');

$menu->addItem('settings', __('Settings'), ['route' => 'app.settings.index'])
    ->allowIfUserCan('list settings');
```

## 2. Checking Via Callbacks

You can also pass callbacks to authorize menu items for users:

```php
$menu = Menus::create('nav', []);
$menu->addItem('users', __('Users'), ['route' => 'app.user.index'])
    ->allowIf(function($user) {
        return $user->id > 500; // Add your arbitrary condition
    });
```

The callback will receive the user as the first parameter.

> You can add multiple `allowIf` and/or `allowIfUserCan` conditions to an item.
> The item will be allowed if **ALL** the conditions will be met.

### Checking Authorization

> By default, items without any condition are allowed.

**To check if an item is allowed:**

```php
$menu->getItem('users')->isAllowed(); // Checks if is allowed for the current user
$menu->getItem('settings')->isAllowed(User::find(501)); // Check if allowed for user 501 
```

**To get the list of allowed children:**

```php
$item->childrenAllowed(); // The allowed child items for the current user
$item->childrenAllowed(\App\User::find(123)); // The allowed children for user 123
```

**In Blade templates:**

```blade
@foreach(menu('nav')->items() as $item)
    @if($item->isAllowed())
       <a href="{!! $item->url() !!}">{{ $item->title }}</a>
    @endif
@endforeach
```

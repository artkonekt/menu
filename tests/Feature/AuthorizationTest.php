<?php
/**
 * Contains the AuthorizationTest class.
 *
 * @copyright   Copyright (c) 2018 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2018-07-23
 *
 */

namespace Konekt\Menu\Tests\Feature;

use Illuminate\Support\Facades\Gate;
use Konekt\Menu\Menu;
use Konekt\Menu\MenuFactory;
use Konekt\Menu\Tests\Dummies\User;
use Konekt\Menu\Tests\TestCase;

class AuthorizationTest extends TestCase
{
    /** @var  Menu */
    protected $menu;

    /** @var User */
    protected $user;

    /** @var User */
    protected $anotherUser;

    /**
     * @test
     */
    public function item_is_allowed_by_default_when_no_auth_condition_has_been_added()
    {
        $item = $this->menu->addItem('home', 'Home', ['url' => '/']);

        $this->assertTrue($item->isAllowed());
    }

    /**
     * @test
     */
    public function simple_conditions_can_be_added_that_will_be_checked_against_user_can()
    {
        $itemAbout = $this->menu->addItem('about', 'About', ['url' => '/about']);
        $itemAbout->allowIfUserCan('see about');

        $itemUsers = $this->menu->addItem('users', 'users', ['url' => '/users']);
        $itemUsers->allowIfUserCan('list users');

        Gate::define('list users', function () {
            return false;
        });

        $this->be($this->user);

        $this->assertFalse($itemAbout->isAllowed());

        Gate::define('see about', function () {
            return true;
        });

        $this->assertTrue($itemAbout->isAllowed());
        $this->assertFalse($itemUsers->isAllowed());
    }

    /**
     * @test
     */
    public function callback_auth_conditions_can_be_added_to_items()
    {
        $item = $this->menu->addItem('new_inquiry', 'New Inquiry');
        $item->allowIf(function ($user) {
            return str_contains($user->email, 'gatto.it');
        });

        $this->be($this->user);

        $this->assertTrue($item->isAllowed());

        $this->be(($this->anotherUser));

        $this->assertFalse($item->isAllowed());
    }

    /**
     * @test
     */
    public function simple_conditions_can_be_checked_against_specific_users()
    {
        $itemAbout = $this->menu->addItem('projects', 'Projects', []);
        $itemAbout->allowIfUserCan('see projects');

        Gate::define('see projects', function ($user) {
            return str_contains($user->email, 'gatto.it');
        });

        $this->assertTrue($itemAbout->isAllowed($this->user));
        $this->assertFalse($itemAbout->isAllowed($this->anotherUser));
    }

    /**
     * @test
     */
    public function callback_conditions_can_be_checked_against_specific_users()
    {
        $itemAbout = $this->menu->addItem('projects', 'Projects', []);
        $itemAbout->allowIf(function ($user) {
            return str_contains($user->email, 'latte');
        });

        $this->assertTrue($itemAbout->isAllowed($this->anotherUser));
        $this->assertFalse($itemAbout->isAllowed($this->user));
    }

    /**
     * @test
     */
    public function multiple_auth_conditions_can_be_added()
    {
        Gate::define('see tits', function ($user) {
            return str_contains($user->email, '.it');
        });

        $item = $this->menu->addItem('tits', 'Tits', []);
        $item->allowIfUserCan('see tits');

        $this->assertTrue($item->isAllowed($this->anotherUser));
        $this->assertTrue($item->isAllowed($this->user));

        $item->allowIf(function ($user) {
            return str_contains($user->email, 'latte');
        });

        $this->assertTrue($item->isAllowed($this->anotherUser));
        $this->assertFalse($item->isAllowed($this->user));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
        $this->setUpAuth();

        $this->menu = MenuFactory::create('nav', []);

        $this->createUsers();
    }

    protected function createUsers()
    {
        $this->user = User::create([
            'email'    => 'giovanni@gatto.it',
            'name'     => 'Giovanni Gatto',
            'password' => bcrypt('Putin')
        ])->fresh();

        $this->anotherUser = User::create([
            'email'    => 'frederico@latte.it',
            'name'     => 'Frederico Latte',
            'password' => bcrypt('Erdogan')
        ]);
    }

    protected function setUpAuth()
    {
        $this->app['config']->set('session.drive', 'array');
        // Use the dummy user class
        $this->app['config']->set('auth.providers.users.model', User::class);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

}

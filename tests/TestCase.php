<?php

declare(strict_types=1);
/**
 * Contains the TestCase class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-16
 *
 */

namespace Konekt\Menu\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\AliasLoader;
use Konekt\Menu\Facades\Menus;
use Konekt\Menu\MenuServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public const APP_URL = 'http://menu.test';
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            MenuServiceProvider::class
        ];
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        AliasLoader::getInstance()->alias('Menu', Menus::class);
    }

    /**
     * @inheritdoc
     */
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);
        $app['config']->set('app.url', self::APP_URL);
    }

    protected function setUpDatabase()
    {
        \Artisan::call('migrate', ['--force' => true]);

        $this->app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
}

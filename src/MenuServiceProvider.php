<?php
/**
 * Contains the Menu service provider class.
 *
 * @author      Lavary
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-16
 *
 */

namespace Konekt\Menu;

use Illuminate\Support\ServiceProvider;
use Konekt\Menu\Renderers\DivItemRenderer;
use Konekt\Menu\Renderers\DivMenuRenderer;
use Konekt\Menu\Renderers\LiItemRenderer;
use Konekt\Menu\Renderers\OlMenuRenderer;
use Konekt\Menu\Renderers\UlMenuRenderer;

class MenuServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('menu', function ($app) {
            return new Repository();
        });

        $this->app->singleton('konekt.menu.renderer.menu.ul', UlMenuRenderer::class);
        $this->app->singleton('konekt.menu.renderer.menu.ol', OlMenuRenderer::class);
        $this->app->singleton('konekt.menu.renderer.menu.div', DivMenuRenderer::class);
        $this->app->singleton('konekt.menu.renderer.item.li', LiItemRenderer::class);
        $this->app->singleton('konekt.menu.renderer.item.div', DivItemRenderer::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['menu'];
    }

}

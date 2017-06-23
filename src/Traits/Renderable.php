<?php
/**
 * Contains the Renderable trait.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-06-23
 *
 */


namespace Konekt\Menu\Traits;


trait Renderable
{
    /** @var string|null    The name of the renderer */
    public $renderer;

    /**
     * @param string $rendererName
     *
     * @return string
     */
    public function render(string $rendererName = null)
    {
        $renderer = app(sprintf('konekt.menu.renderer.%s.%s',
            snake_case(class_basename(static::class)),
            $rendererName ?: $this->renderer
        ));

        return $renderer->render($this);
    }

}
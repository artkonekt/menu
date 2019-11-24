<?php
/**
 * Contains the PhpUnit6Compatible trait.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-11-24
 *
 */

namespace Konekt\Menu\Tests;

trait PhpUnit6Compatible
{
    public function __call($name, $arguments)
    {
        if ('assertStringContainsString' === $name) {
            self::assertContains(...$arguments);
        }
    }
}

<?php

declare(strict_types=1);

namespace Labrodev\Crud\Exceptions;

use Exception;

final class ActionMethodMissed extends Exception
{
    /**
     * @param string $class
     * @param string $method
     * @return self
     */
    public static function make(
        string $class,
        string $method
    ): self {
        return new self(
            "Action `{$class}` is missed method `{$method}`"
        );
    }
}

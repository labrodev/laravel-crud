<?php

declare(strict_types=1);

namespace Labrodev\Crud\Exceptions;

use Exception;

final class ActionAttributeMissed extends Exception
{
    /**
     * @param string $class
     * @param string $attribute
     * @return self
     */
    public static function make(
        string $class,
        string $attribute
    ): self {
        return new self(
            "Action `{$class}` is missed method `{$attribute}`"
        );
    }
}

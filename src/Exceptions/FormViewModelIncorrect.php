<?php

declare(strict_types=1);

namespace Labrodev\Crud\Exceptions;

use Exception;

final class FormViewModelIncorrect extends Exception
{
    /**
     * @param string $formViewModel
     * @return self
     */
    public static function make(string $formViewModel): self
    {
        return new self($formViewModel);
    }
}

<?php

namespace Labrodev\Crud\ViewModels;

use Labrodev\Crud\ViewModels\FormViewModelInterface;
use Spatie\ViewModels\ViewModel;

abstract class FormViewModel extends ViewModel implements FormViewModelInterface
{
    /**
     * @return string
     */
    abstract public function title(): string;
    /**
     * @return string
     */
    abstract public function formAction(): string;

    /**
     * @return boolean
     */
    abstract public function formMethodPut(): bool;

    /**
     * @return string|null
     */
    abstract public function backHref(): ?string;
}

<?php

namespace Labrodev\Crud\ViewModels;

interface FormViewModelInterface
{
    /**
     * @return string
     */
    public function formAction(): string;

    /**
     * @return boolean
     */
    public function formMethodPut(): bool;

    /**
     * @return string|null
     */
    public function backHref(): ?string;

    /**
     * @return string
     */
    public function title(): string;
}

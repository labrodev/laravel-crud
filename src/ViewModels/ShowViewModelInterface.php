<?php

namespace Labrodev\Crud\ViewModels;

use Spatie\ViewModels\ViewModel;

interface ShowViewModelInterface
{
    /**
     * @return string|null
     */
    public function backHref(): ?string;

    /**
     * @return string
     */
    public function title(): string;
}

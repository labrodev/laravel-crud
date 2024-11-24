<?php

namespace Labrodev\Crud\ViewModels;

use Labrodev\Crud\ViewModels\ShowViewModelInterface;
use Spatie\ViewModels\ViewModel;

abstract class ShowViewModel extends ViewModel implements ShowViewModelInterface
{
    /**
     * @return string|null
     */
    abstract public function backHref(): ?string;

    /**
     * @return string
     */
    abstract public function title(): string;

    /**
     * @return string
     */
    abstract protected function routePrefix(): string;
}

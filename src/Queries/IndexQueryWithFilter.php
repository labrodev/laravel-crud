<?php

namespace Labrodev\Crud\Queries;

interface IndexQueryWithFilter
{
    /**
     * @return void
     */
    public function configureAllowedFilters(): void;
}

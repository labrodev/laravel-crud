<?php

namespace Labrodev\Crud\Queries;

interface IndexQueryWithSort
{
    /**
     * @return void
     */
    public function configureAllowedSorts(): void;
}

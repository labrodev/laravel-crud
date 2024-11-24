<?php

namespace Labrodev\Crud\ViewModels;

interface ControllerWithCrud
{
    public const EDIT_METHOD = 'edit';
    public const INDEX_METHOD = 'index';

    public const ITEMS_PER_PAGE_VALUE_VARIABLE = 'itemsPerPageValue';
    public const ITEMS_PER_PAGE_QUERY_PARAMETER = 'per_page';
    public const SORT_QUERY_PARAMETER = 'sort';
    public const FILTER_QUERY_PARAMETER = 'filter';
    public const ITEMS_PER_PAGE_OPTIONS_VARIABLE = 'itemsPerPageOptions';
    public const DEFAULT_ITEMS_PER_PAGE = 15;
}

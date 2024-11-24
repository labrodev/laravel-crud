<?php

use Labrodev\Crud\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

afterEach(function () {
    Mockery::close();
});

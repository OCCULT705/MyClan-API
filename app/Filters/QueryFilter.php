<?php

namespace App\Filters;

abstract class QueryFilter
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }
}

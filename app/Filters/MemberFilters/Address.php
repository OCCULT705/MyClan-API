<?php

namespace App\Filters\MemberFilters;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;

class Address extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->where("address", "like", "%{$value}%");
    }
}

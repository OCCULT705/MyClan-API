<?php

namespace App\Filters\MemberFilters;

use App\Filters\FilterContract;
use App\Filters\QueryFilter;

class Name extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->where("firstname", "like", "%{$value}%")
                    ->orWhere("middlename", "like", "%{$value}%")
                    ->orWhere("lastname", "like", "%{$value}%")
                    ->orWhere("givenname", "like", "%{$value}%");
    }
}

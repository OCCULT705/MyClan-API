<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PartnerOf implements Rule
{
    protected $member;

    /**
     * Create a new partner_of rule instance.
     *
     * @param string $member The id of the clan member
     * @return void
     */
    public function __construct($member = null)
    {
        $this->member = $member;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($this->member == null) return true;
        $partner = $this->member;
        $records = DB::table('spouses')->where(function($query) use($value, $partner){
            $query->where('member_id', '=', $value)->where('partner_id', '=', $partner);
        })->orWhere(function($query) use($value, $partner){
            $query->where('member_id', '=', $partner)->where('partner_id', '=', $value);
        })->get()->toArray();
        return (count($records) > 0);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The selected mother is not a partner of the selected father.';
    }
}

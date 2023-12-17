<?php

namespace App\Rules;

use App\Models\Member;
use Illuminate\Contracts\Validation\Rule;

class OppositeGender implements Rule
{

    protected $gender;

    /**
     * Create a new opposite gender rule instance.
     *
     * @param string $gender The character representing the gender of a clan member
     * @return void
     */
    public function __construct($gender = null)
    {
        $this->gender = $gender;
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
        $spouse = Member::where('id', '=', $value)->get()->first();
        return (strval($spouse->gender) !== strval($this->gender));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The spouse should be of opposite gender.';
    }
}

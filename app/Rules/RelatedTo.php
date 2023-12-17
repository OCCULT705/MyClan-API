<?php

namespace App\Rules;

use App\Traits\Relatives;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class RelatedTo implements Rule
{
    use Relatives;

    protected $father_id;
    protected $mother_id;
    protected $binary;

    /**
     * Create a new related rule instance.
     *
     * @param string $father_id The id of the father of clan member to be tested as related with
     * @param string $mother_id The id of the mother of clan member to be tested as related with
     * @param boolean $binary [optional] If the reverse_related algorithm is set to true, then the rule will return false when the two clan members are related.
     * @return void
     */
    public function __construct($father_id = null, $mother_id = null, $binary = false)
    {
        $this->father_id = $father_id;
        $this->mother_id = $mother_id;
        $this->binary = $binary;
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
        $ascendants = DB::table('ascendants')->select('member_id','ascendant_id')->get()->toArray();
        $temp = DB::table('spouses')->where('member_id', '=', $value)->get(["partner_id"])->toArray();
        $spouses = [];
        for ($i=0; $i < count($temp); $i++) {
            array_push($spouses, $temp[$i]->partner_id);
        }
        if($this->isRelated($ascendants, [$this->father_id, $this->mother_id], $value, $spouses)){
            return (!$this->binary);
        }else{
            return $this->binary;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if($this->binary){
            return 'The selected clan member is a relative';
        }else{
            return 'The selected clan member is not a relative';
        }
    }
}

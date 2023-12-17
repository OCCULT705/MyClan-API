<?php

namespace App\Http\Requests;

use App\Rules\FemaleParent;
use App\Rules\MaleParent;
use App\Rules\PartnerOf;
use Illuminate\Foundation\Http\FormRequest;

class ChangeParentsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'father' => [
                'bail','required','string','max:100','exists:App\Models\Member,id',
                new MaleParent()
            ],
            'mother' => [
                'bail','required','string','max:100','different:father','exists:App\Models\Member,id',
                new FemaleParent(),
                new PartnerOf($this->father)
            ]
        ];
    }

    /**
     * Get the validation messages that apply to the specified rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'father.required' => 'The father of the clan member is required.',
            'father.string' => 'The selected clan member has an invalid index.',
            'father.max' => 'The selected clan member has a very long index.',
            'father.exists' => 'The selected clan member does not exist.',
            'mother.required' => 'The mother of the clan member is required.',
            'mother.string' => 'The selected clan member has an invalid index.',
            'mother.max' => 'The selected clan member has a very long index.',
            'mother.different' => 'The selected options for mother and father must be different.',
            'mother.exists' => 'The selected clan member does not exist.',
        ];
    }
}

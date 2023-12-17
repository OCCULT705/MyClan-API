<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
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
            'firstname' => 'bail|required|string|between:1,50',
            'middlename' => 'bail|nullable|string|between:1,50',
            'lastname' => 'bail|required|string|between:1,50',
            'givenname' => 'bail|nullable|string|between:1,50',
            'gender' => ['bail','required','string','size:1',Rule::in(['M', 'F'])],
            'birth' => 'bail|required|date|before:today',
            'death' => 'bail|nullable|date|before:today',
            'address' => 'bail|nullable|string|between:1,100',
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
            'firstname.required' => 'First name is required.',
            'firstname.string' => 'First name must be a string.',
            'firstname.between' => 'First name must be 1 to 50 characters long',
            'middlename.string' => 'Middle name must be a string.',
            'middlename.between' => 'Middle name must be 1 to 50 characters long',
            'lastname.required' => 'Last name is required.',
            'lastname.string' => 'Last name must be a string.',
            'lastname.between' => 'Last name must be 1 to 50 characters long',
            'givenname.string' => 'Given name must be a string.',
            'givenname.between' => 'Given name must be 1 to 50 characters long',
            'gender.required' => 'Gender is required.',
            'gender.string' => 'Gender must be a string.',
            'gender.size' => 'Gender must be 1 character long',
            'gender.in' => 'Gender should be either M(for Male) or F(for Female)',
            'birth.required' => 'Date of birth is required.',
            'birth.date' => 'Date of birth is not a valid date.',
            'birth.before' => 'Date of birth must be a date before today.',
            'death.date' => 'Date of death is not a valid date.',
            'death.before' => 'Date of death must be a date before today.',
            'address.string' => 'Address must be a string.',
            'address.between' => 'Address must be 1 to 100 characters long',
        ];
    }
}

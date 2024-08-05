<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'subject' => ['required', 'max:255'],
            'Companies_id' => ['exclude_if:Companies_id,null', 'exists:Companies,id'],
            'body' => ['required'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'subject.required' => __('The :attribute field is required', ['attribute' => __('subject')]),
            'subject.max' => __('The :attribute may not be greater than :max characters', ['attribute' => __('subject'), 'max' => 255]),

            'Companies_id.exists' => __('The selected :attribute is invalid', ['attribute' => __('Companies')]),

            'body.required' => __('The :attribute field is required', ['attribute' => __('body')]),
        ];
    }
}

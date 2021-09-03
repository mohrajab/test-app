<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->product && request()->user()->cannot('update', $this->product))
            return false;

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
            'name' => ['required'],
            'manufactured_year' => ['required', 'integer', 'min:1990', 'max:' . Carbon::now()->year],
            'photo' => [Rule::requiredIf(function () {
                return !$this->product;
            }), 'image']
        ];
    }
}

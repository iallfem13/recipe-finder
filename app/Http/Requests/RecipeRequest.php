<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RecipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'filters' => 'required|json',
            'q' => 'null|string'
        ];
    }

    /**
     * @param Validator $validator
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Invalid request body',
            'data'    => $validator->errors()
        ]));
    }

    /**
     * Prepare filters for validation
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('filters')) {
            $filters = json_decode($this->filters, true);

            if (empty($filters)) {
                // trigger validation for required filters
                $this->merge(['filters' => null]);
            }
        }
    }
}

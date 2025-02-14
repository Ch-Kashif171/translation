<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class TranslateRequest extends FormRequest
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
           // 'locale' => 'required|string|max:10|unique:translations',
            'locale' => ['required', Rule::unique('translations')->where(fn ($query) =>
            $query->where('key', request('key')) )->ignore($this->translation)],
            'key' => ['required', Rule::unique('translations')->where(fn ($query) =>
            $query->where('value', request('value')) )->ignore($this->translation)],
            'value' => 'required|string',
            'tags' => 'sometimes',
            'tags.*' => 'required'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->validationError($validator)
        );
    }

    protected function validationError($validation): JsonResponse
    {
        $fieldMessages = $validation->errors();
        $errorMsg = $fieldMessages->first();
        $validation = [
            'status' => false,
            'message' => $errorMsg,
            'errors' => $fieldMessages
        ];
        return response()->json($validation, 422);
    }


}

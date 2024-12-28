<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMangaLinkRequest extends FormRequest
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
            'url' => 'required|url|unique:manga_links,url,' . $this->route('id') . ',id,anime_id,' . $this->anime_id,
            'anime_id' => 'required|exists:animes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'The URL is required.',
            'url.url' => 'The URL must be a valid URL.',
            'url.unique' => 'The URL has already been added for this anime.',
            'anime_id.required' => 'The anime ID is required.',
            'anime_id.exists' => 'The selected anime ID is invalid.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}

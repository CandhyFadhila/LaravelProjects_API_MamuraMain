<?php

namespace App\Http\Requests;

use App\Http\Resources\Templates\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreInternalImageRequest extends FormRequest
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
            'intern_image_be' => ['nullable', 'array', 'min:1', 'max:5'],
            'intern_image_be.*' => ['nullable', 'mimes:jpg,jpeg,png', 'max:10240'],
            'delete_document_ids' => ['nullable', 'array'],
            'delete_document_ids.*' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'intern_image_be.required' => 'Gambar tidak boleh kosong.',
            'intern_image_be.array' => 'Gambar harus berupa array.',
            'intern_image_be.min' => 'Minimal gambar yang diunggah adalah 1 gambar.',
            'intern_image_be.max' => 'Maksimal gambar yang diunggah adalah 5 gambar.',
            'intern_image_be.*.mimes' => 'Gambar hanya boleh berupa JPG, JPEG, dan PNG.',
            'intern_image_be.*.max' => 'Ukuran gambar maksimal 10MB.',
            'delete_document_ids.array' => 'Format yang dihapus harus berupa array.',
            'delete_document_ids.*.integer' => 'ID yang dihapus harus berupa angka.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $messages = implode(' ', $validator->errors()->all());
        $response = new WithoutDataResource(
            Response::HTTP_BAD_REQUEST,
            'FAILED_VALIDATION',
            'Format Data Tidak Sesuai Ketentuan',
            $messages
        );

        throw new HttpResponseException(response()->json($response, Response::HTTP_BAD_REQUEST));
    }
}

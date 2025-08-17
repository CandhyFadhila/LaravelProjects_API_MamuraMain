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
            'intern_image_be'   => ['nullable', 'array', 'max:5', 'required_without:delete_document_ids'],
            'intern_image_be.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'delete_document_ids'   => ['nullable', 'array', 'required_without:intern_image_be'],
            'delete_document_ids.*' => ['integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'intern_image_be.required_without'    => 'Unggah minimal satu gambar atau sertakan delete_document_ids.',
            'intern_image_be.array'               => 'Gambar harus berupa array.',
            'intern_image_be.max'                 => 'Maksimal gambar yang diunggah adalah 5.',
            'intern_image_be.*.file'              => 'Setiap item harus berupa file.',
            'intern_image_be.*.image'             => 'Setiap file harus berupa gambar.',
            'intern_image_be.*.mimes'             => 'Format yang diizinkan: JPG, JPEG, PNG, atau WEBP.',
            'intern_image_be.*.max'               => 'Ukuran maksimal setiap gambar adalah 10MB.',

            'delete_document_ids.required_without'=> 'Sertakan delete_document_ids atau unggah minimal satu gambar.',
            'delete_document_ids.array'           => 'Format yang dihapus harus berupa array.',
            'delete_document_ids.*.integer'       => 'ID yang dihapus harus berupa angka.',
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

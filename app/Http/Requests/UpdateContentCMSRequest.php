<?php

namespace App\Http\Requests;

use App\Http\Resources\Templates\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdateContentCMSRequest extends FormRequest
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
            'content_type_id' => ['required', 'exists:content_types,id'],
            'content_file_id' => ['required', 'array', 'min:1', 'max:5'],
            'content_file_id.*' => ['required', 'mimes:jpg,jpeg,png,pdf,doc,docx,ppt,pptx', 'max:10240'],
            'content' => ['required'],
            'delete_content_file_ids' => ['nullable', 'array'],
            'delete_content_file_ids.*' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'content_type_id.required' => 'Tipe konten tidak boleh kosong.',
            'content_type_id.exists' => 'Tipe konten tersebut tidak valid.',
            'content_file_id.required' => 'File konten tidak boleh kosong.',
            'content_file_id.array' => 'File konten mharus berupa array.',
            'content_file_id.min' => 'Minimal file konten yang diunggah adalah 1 gambar.',
            'content_file_id.max' => 'Maksimal file konten yang diunggah adalah 5 gambar.',
            'content_file_id.*.mimes' => 'File hanya boleh berupa JPG, JPEG, PNG, PDF, DOC, DOCX, PPT, dan PPTX.',
            'content_file_id.*.max' => 'Ukuran file konten maksimal 10MB.',
            'content.required' => 'Isi konten tidak boleh kosong.',
            'delete_content_file_ids.array' => 'Format thumbnail yang dihapus harus berupa array.',
            'delete_content_file_ids.*.integer' => 'ID thumbnail yang dihapus harus berupa angka.',
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

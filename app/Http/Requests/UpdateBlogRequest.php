<?php

namespace App\Http\Requests;

use App\Http\Resources\Templates\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdateBlogRequest extends FormRequest
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
            'blog_category_id' => ['required', 'exists:blog_categories,id'],
            'thumbnail_id' => ['nullable', 'array', 'min:1', 'max:5'],
            'thumbnail_id.*' => ['nullable', 'mimes:jpg,jpeg,png', 'max:10240'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'blog_content' => ['required'],
            'delete_thumbnail_ids' => ['nullable', 'array'],
            'delete_thumbnail_ids.*' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'blog_category_id.required' => 'Kategori blog tidak boleh kosong.',
            'blog_category_id.exists' => 'Kategori blog tersebut tidak valid.',
            'thumbnail_id.required' => 'Gambar thumbnail kategori blog tidak boleh kosong.',
            'thumbnail_id.array' => 'Gambar thumbnail harus berupa array.',
            'thumbnail_id.min' => 'Minimal gambar thumbnail yang diunggah adalah 1 gambar.',
            'thumbnail_id.max' => 'Maksimal gambar thumbnail yang diunggah adalah 5 gambar.',
            'thumbnail_id.*.mimes' => 'Gambar thumbnail hanya boleh berupa JPG, JPEG, dan PNG.',
            'thumbnail_id.*.max' => 'Ukuran gambar thumbnail maksimal 10MB.',
            'title.required' => 'Judul blog tidak boleh kosong.',
            'title.string' => 'Judul blog harus berupa string.',
            'title.max' => 'Panjang judul blog maksimal 255 karakter.',
            'slug.required' => 'Slug blog tidak boleh kosong.',
            'slug.string' => 'Slug blog harus berupa string.',
            'slug.max' => 'Panjang slug blog maksimal 255 karakter.',
            'description.required' => 'Deskripsi blog tidak boleh kosong.',
            'description.string' => 'Deskripsi blog harus berupa string.',
            'blog_content.required' => 'Konten blog tidak boleh kosong.',
            'delete_thumbnail_ids.array' => 'Format thumbnail blog yang dihapus harus berupa array.',
            'delete_thumbnail_ids.*.integer' => 'ID thumbnail blog yang dihapus harus berupa angka.',
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

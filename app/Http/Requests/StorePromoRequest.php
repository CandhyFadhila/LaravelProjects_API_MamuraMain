<?php

namespace App\Http\Requests;

use App\Http\Resources\Templates\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StorePromoRequest extends FormRequest
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
            'promo_banner_id' => ['nullable', 'array', 'max:1'],
            'promo_banner_id.*' => ['nullable', 'mimes:jpg,jpeg,png', 'max:10240'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'terms' => ['required'],
            'promo_value' => ['nullable', 'integer'],
            'promo_end' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'promo_banner_id.array' => 'Gambar promo harus berupa array.',
            'promo_banner_id.max' => 'Maksimal gambar promo yang diunggah adalah 1 gambar.',
            'promo_banner_id.*.mimes' => 'Gambar promo hanya boleh berupa JPG, JPEG, dan PNG.',
            'promo_banner_id.*.max' => 'Ukuran gambar promo maksimal 10MB.',
            'name.required' => 'Nama promo tidak boleh kosong.',
            'name.string' => 'Nama promo harus berupa string.',
            'name.max' => 'Panjang nama promo maksimal 255 karakter.',
            'description.string' => 'Deskripsi promo harus berupa string.',
            'terms.required' => 'Syarat dan ketentuan tidak boleh kosong.',
            'promo_value.required' => 'Besaran nilai promo tidak boleh kosong.',
            'promo_value.integer' => 'Besaran nilai promo harus berupa angka.',
            'promo_end.required' => 'Tanggal berakhir promo tidak boleh kosong.',
            'promo_end.date' => 'Tanggal berakhir promo harus berupa tanggal valid.',
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

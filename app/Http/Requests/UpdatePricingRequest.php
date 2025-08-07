<?php

namespace App\Http\Requests;

use App\Http\Resources\Templates\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdatePricingRequest extends FormRequest
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
            'pricing_category_id' => ['required', 'exists:pricing_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'internet_speed' => ['required', 'integer'],
            'price' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
            'is_recommended' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'pricing_category_id.required' => 'Kategori harga paket tidak boleh kosong.',
            'pricing_category_id.exists' => 'Kategori harga paket tersebut tidak valid.',
            'name.required' => 'Nama harga paket tidak boleh kosong.',
            'name.string' => 'Nama harga paket harus berupa string.',
            'name.max' => 'Panjang nama harga paket maksimal 255 karakter.',
            'internet_speed.required' => 'Kecepatan internet tidak boleh kosong.',
            'internet_speed.integer' => 'Kecepatan internet harus berupa angka.',
            'price.required' => 'Harga paket tidak boleh kosong.',
            'price.integer' => 'Harga paket harus berupa angka.',
            'description.string' => 'Deskripsi harga paket harus berupa string.',
            'is_recommended.boolean' => 'Rekomendasi harga paket harus berupa 1 atau 0.',
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

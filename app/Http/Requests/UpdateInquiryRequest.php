<?php

namespace App\Http\Requests;

use App\Http\Resources\Templates\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdateInquiryRequest extends FormRequest
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
            'message' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kontak tidak boleh kosong.',
            'name.string' => 'Nama kontak harus berupa string.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.string' => 'Email harus berupa string.',
            'email.email' => 'Email harus berupa email yang valid.',
            'phone_number.required' => 'Nomor telepon tidak boleh kosong.',
            'phone_number.string' => 'Nomor telepon harus berupa string.',
            'phone_number.max' => 'Panjang nomor telepon maksimal 13 angka.',
            'address.required' => 'Alamat tidak boleh kosong.',
            'address.string' => 'Alamat harus berupa string.',
            'message.required' => 'Pesan tidak boleh kosong.',
            'message.string' => 'Pesan harus berupa string.',
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

<?php

namespace App\Http\Requests;

use App\Http\Resources\Templates\WithoutDataResource;
use App\Rules\ValidRecaptcha;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreJobApplicationRecaptchaRequest extends FormRequest
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
            'carrier_id' => ['required', 'exists:carriers,id'],
            'resume_id' => ['required', 'array', 'max:1'],
            'resume_id.*' => ['required', 'mimes:pdf', 'max:10240'],
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'phone_number' => ['required', 'max:13'],
            'captcha_token'=> ['required', new ValidRecaptcha('carrier_apply')],
        ];
    }

    public function messages(): array
    {
        return [
            'carrier_id.required' => 'Karir tidak boleh kosong.',
            'carrier_id.exists' => 'Karir tersebut tidak valid.',
            'resume_id.required' => 'Dokumen resume tidak boleh kosong.',
            'resume_id.array' => 'Dokumen resume harus berupa array.',
            'resume_id.max' => 'Maksimal dokumen resume yang diunggah adalah 1 dokumen.',
            'resume_id.*.mimes' => 'Dokumen resume hanya boleh berupa PDF.',
            'resume_id.*.max' => 'Ukuran dokumen resume maksimal 10MB.',
            'name.required' => 'Nama pendaftar tidak boleh kosong.',
            'name.string' => 'Nama pendaftar harus berupa string.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.string' => 'Email harus berupa string.',
            'email.email' => 'Email harus berupa email yang valid.',
            'phone_number.required' => 'Nomor telepon tidak boleh kosong.',
            'phone_number.string' => 'Nomor telepon harus berupa string.',
            'phone_number.max' => 'Panjang nomor telepon maksimal 13 angka.',
            'captcha_token.required' => 'Token reCAPTCHA tidak boleh kosong.',
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

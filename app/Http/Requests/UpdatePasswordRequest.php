<?php

namespace App\Http\Requests;

use App\Http\Resources\Templates\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdatePasswordRequest extends FormRequest
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
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed', 'different:current_password'],
            'password_confirmation' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => 'Kata sandi lama tidak boleh kosong.',
            'current_password.current_password' => 'Kata sandi lama yang Anda masukkan tidak sesuai.',
            'password.required' => 'Kata sandi baru tidak boleh kosong.',
            'password.min' => 'Kata sandi baru terlalu pendek, minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai dengan kata sandi baru.',
            'password.different' => 'Kata sandi baru tidak boleh sama dengan kata sandi lama.',
            'password_confirmation.required' => 'Konfirmasi kata sandi tidak boleh kosong.',
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

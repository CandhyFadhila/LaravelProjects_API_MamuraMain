<?php

namespace App\Http\Requests;

use App\Http\Resources\Templates\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreCarrierRequest extends FormRequest
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
            'carrier_category_id' => ['required', 'exists:carrier_categories,id'],
            'employee_status_id' => ['required', 'exists:employee_statuses,id'],
            'job_location_id' => ['required', 'exists:job_locations,id'],
            'qualification' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'carrier_category_id.required' => 'Kategori karir tidak boleh kosong.',
            'carrier_category_id.exists' => 'Kategori karir tersebut tidak valid.',
            'employee_status_id.required' => 'Status karyawan tidak boleh kosong.',
            'employee_status_id.exists' => 'Status karyawan tersebut tidak valid.',
            'job_location_id.required' => 'Penempatan kerja karyawan tidak boleh kosong.',
            'job_location_id.exists' => 'Penempatan kerja karyawan tersebut tidak valid.',
            'qualification.required' => 'Kualifikasi karyawan tidak boleh kosong.',
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

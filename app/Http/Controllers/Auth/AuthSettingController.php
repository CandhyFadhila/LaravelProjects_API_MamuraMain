<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\DocumentHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdatePhotoProfileRequest;
use App\Http\Resources\Templates\WithoutDataResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthSettingController extends Controller
{
    public function updatePassword(UpdatePasswordRequest $request)
    {
        try {
            if (!Gate::allows('masterdata.edit')) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_FORBIDDEN,
                        'NO_ACCESS',
                        'Tidak Memiliki Akses',
                        'Anda tidak memiliki akses untuk mengakses halaman ini.',
                    ),
                    Response::HTTP_FORBIDDEN
                );
            }

            $user = $request->user();

            // 🔒 Batasi hanya Super Admin
            if (!$user->hasRole('Super Admin')) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_FORBIDDEN,
                        'NO_ACCESS_ROLE',
                        'Akses Ditolak',
                        'Hanya pengguna dengan role Super Admin yang dapat mengganti password.',
                    ),
                    Response::HTTP_FORBIDDEN
                );
            }

            DB::beginTransaction();

            // ✅ Pastikan password lama benar
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_BAD_REQUEST,
                        'INVALID_CURRENT_PASSWORD',
                        'Kata Sandi Lama Salah',
                        'Kata sandi lama yang Anda masukkan tidak sesuai.'
                    ),
                    Response::HTTP_BAD_REQUEST
                );
            }

            // ✅ Cegah password baru sama dengan lama
            if (Hash::check($request->password, $user->password)) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_BAD_REQUEST,
                        'PASSWORD_NOT_CHANGED',
                        'Kata Sandi Baru Tidak Valid',
                        'Kata sandi baru tidak boleh sama dengan kata sandi lama.'
                    ),
                    Response::HTTP_BAD_REQUEST
                );
            }

            // ✅ Update password & timestamp
            $user->forceFill([
                'password'              => Hash::make($request->password),
                'last_change_password'  => now(),
            ])->save();

            // 🔐 Revoke token Sanctum
            $currentToken = method_exists($request->user(), 'currentAccessToken')
                ? $request->user()->currentAccessToken()
                : null;

            if (method_exists($user, 'tokens')) {
                if ($currentToken) {
                    // Hapus semua token lain, pertahankan token saat ini
                    $user->tokens()->where('id', '!=', $currentToken->id)->delete();
                } else {
                    // Jika bukan via token (mis. via session), hapus semua token
                    $user->tokens()->delete();
                }
            }

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_DATA',
                    'Berhasil Memperbarui Data',
                    "Kata sandi anda berhasil diperbarui."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('change_password_dashboard')->error('| Auth Setting | - Error function updatePassword : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_GET_DATA',
                    'Gagal Mengambil Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function updatePhotoProfile(UpdatePhotoProfileRequest $request)
    {
        try {
            if (!Gate::allows('masterdata.edit')) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_FORBIDDEN,
                        'NO_ACCESS',
                        'Tidak Memiliki Akses',
                        'Anda tidak memiliki akses untuk mengakses halaman ini.',
                    ),
                    Response::HTTP_FORBIDDEN
                );
            }

            $user = $request->user();

            // 🔒 Batasi hanya Super Admin
            if (!$user->hasRole('Super Admin')) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_FORBIDDEN,
                        'NO_ACCESS_ROLE',
                        'Akses Ditolak',
                        'Hanya pengguna dengan role Super Admin yang dapat mengganti foto profil.',
                    ),
                    Response::HTTP_FORBIDDEN
                );
            }

            DB::beginTransaction();

            // Normalisasi existing & payload
            $existingIds = is_array($user->photo_profile_id)
                ? $user->photo_profile_id
                : (json_decode($user->photo_profile_id, true) ?: []);

            $newUploads = $request->file('photo_profile_id') ?? [];

            // Skenario A: belum ada foto → langsung upload & set
            if (empty($existingIds)) {
                $newDocumentIds = DocumentHelper::uploadDocuments($newUploads);
                $user->update(['photo_profile_id' => $newDocumentIds ?: null]);

                DB::commit();
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_OK,
                        'SUCCESS_UPDATE_DATA',
                        'Berhasil Memperbarui Data',
                        'Foto profil berhasil ditambahkan.'
                    ),
                    Response::HTTP_OK
                );
            }

            // Skenario B: sudah ada foto + ada payload baru → hapus lama → null → upload baru → hapus record doc lama
            if (!empty($existingIds) && !empty($newUploads)) {
                // 2.1.1 & 2.1.4: hapus file lama dari storage server + hapus record dokumen
                DocumentHelper::deleteDocuments($existingIds);

                // 2.1.2: set field user ke null
                $user->update(['photo_profile_id' => null]);

                // 2.1.3: upload foto baru
                $newDocumentIds = DocumentHelper::uploadDocuments($newUploads);

                // set field user ke dokumen baru
                $user->update(['photo_profile_id' => $newDocumentIds ?: null]);

                DB::commit();
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_OK,
                        'SUCCESS_UPDATE_DATA',
                        'Berhasil Memperbarui Data',
                        'Foto profil berhasil diperbarui.'
                    ),
                    Response::HTTP_OK
                );
            }

            // Jika sampai sini, berarti ada kondisi tak terduga (mis. tidak ada file baru padahal sudah ada foto)
            DB::rollBack();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_BAD_REQUEST,
                    'INVALID_OPERATION',
                    'Operasi Tidak Valid',
                    'Tidak ada perubahan yang dilakukan pada foto profil.'
                ),
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('change_profile_dashboard')->error('| Auth Setting | - Error function updatePhotoProfile : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_GET_DATA',
                    'Gagal Mengambil Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}

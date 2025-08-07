<?php

namespace App\Http\Controllers\CoverageArea;

use App\Helpers\QueryFilterSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupportedProvinceRequest;
use App\Http\Requests\UpdateSupportedProvinceRequest;
use App\Http\Resources\CoverageArea\SupportedProvinceResource;
use App\Http\Resources\Templates\WithDataResource;
use App\Http\Resources\Templates\WithoutDataResource;
use App\Models\SupportedProvince;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class SupportedProvinceController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!Gate::allows('masterdata.view')) {
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

            $query = SupportedProvince::withTrashed();

            if ($request->has('search')) {
                $query = QueryFilterSearch::applySearch($query, $request->input('search'), [
                    'name'
                ]);
            }

            $result = QueryFilterSearch::applyPagination($query, $request);
            if ($result->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_OK,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Tidak ada data yang sesuai dengan filter atau pencarian.'
                    ),
                    Response::HTTP_OK
                );
            }

            if ($result instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                $data = QueryFilterSearch::formatPaginationCollection($result, SupportedProvinceResource::class);
            } else {
                $data = [
                    'data' => SupportedProvinceResource::collection($result),
                    'pagination' => null
                ];
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data provinsi yang disupport berhasil didapatkan.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('province_supported')->error('| Index | - Error function index : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function store(StoreSupportedProvinceRequest $request)
    {
        try {
            if (!Gate::allows('masterdata.create')) {
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

            DB::beginTransaction();

            $duplicate = SupportedProvince::where('name', $request->name)
                ->whereNull('deleted_at')
                ->exists();
            if ($duplicate) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_CONFLICT,
                        'DUPLICATE_NAME',
                        'Duplikat Data',
                        "Nama provinsi yang disupport '{$request->name}' sudah digunakan oleh data lain yang aktif. Silakan gunakan nama lain."
                    ),
                    Response::HTTP_CONFLICT
                );
            }

            SupportedProvince::create([
                'name' => $request->name,
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_CREATED,
                    'SUCCESS_CREATE_DATA',
                    'Berhasil Menyimpan Data',
                    "Data provinsi yang disupport '{$request->name}' berhasil ditambahkan."
                ),
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('province_supported')->error('| Store | - Error function store : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function show($id)
    {
        try {
            if (!Gate::allows('masterdata.view')) {
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

            $supportedProvince = SupportedProvince::withTrashed()->find($id);
            if (!$supportedProvince) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Nama provinsi yang disupport dengan ID tersebut tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    "Detail data provinsi yang disupport '{$supportedProvince->name}' berhasil didapatkan.",
                    new SupportedProvinceResource($supportedProvince)
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('province_supported')->error('| Detail | - Error function show : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function update(UpdateSupportedProvinceRequest $request, $id)
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

            DB::beginTransaction();

            $supportedProvince = SupportedProvince::withTrashed()->find($id);
            if (!$supportedProvince) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Nama provinsi yang disupport dengan ID tersebut tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $duplicate = SupportedProvince::where('name', $request->name)
                ->whereNull('deleted_at')
                ->where('id', '!=', $id)
                ->exists();
            if ($duplicate) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_CONFLICT,
                        'DUPLICATE_NAME',
                        'Duplikat Data',
                        "Nama provinsi yang disupport '{$request->name}' sudah digunakan pada data yang sama."
                    ),
                    Response::HTTP_CONFLICT
                );
            }

            $supportedProvince->update([
                'name' => $request->name,
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_DATA',
                    'Berhasil Memperbarui Data',
                    "Data provinsi yang disupport '{$supportedProvince->name}' berhasil diperbarui."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('province_supported')->error('| Update | - Error function update : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function destroy($id)
    {
        try {
            if (!Gate::allows('masterdata.delete')) {
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

            DB::beginTransaction();

            $supportedProvince = SupportedProvince::find($id);
            if (!$supportedProvince) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Nama provinsi yang disupport dengan ID tersebut tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $supportedProvince->delete();

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_DELETE_DATA',
                    'Berhasil Menghapus Data',
                    "Data provinsi yang disupport '{$supportedProvince->name}' berhasil dihapus."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('province_supported')->error('| Destroy | - Error function destroy : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function restore($id)
    {
        try {
            if (!Gate::allows('masterdata.restore')) {
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

            DB::beginTransaction();

            $supportedProvince = SupportedProvince::onlyTrashed()->find($id);
            if (!$supportedProvince) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Nama provinsi yang disupport dengan ID tersebut tidak ditemukan atau belum dihapus.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            // Validasi unik
            $duplicate = SupportedProvince::where('name', $supportedProvince->name)->whereNull('deleted_at')->exists();
            if ($duplicate) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_CONFLICT,
                        'DUPLICATE_NAME',
                        'Duplikat Data',
                        "Nama provinsi yang disupport '{$supportedProvince->name}' sudah digunakan oleh entri aktif lain. Silakan ubah nama terlebih dahulu sebelum merestore."
                    ),
                    Response::HTTP_CONFLICT
                );
            }

            $supportedProvince->restore();

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_RESTORE_DATA',
                    'Berhasil Mengembalikan Data',
                    "Data provinsi yang disupport '{$supportedProvince->name}' berhasil dikembalikan."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('province_supported')->error('| Restore | - Error function restore : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

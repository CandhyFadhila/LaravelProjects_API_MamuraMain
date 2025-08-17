<?php

namespace App\Http\Controllers\Pricing;

use App\Helpers\QueryFilterSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePricingRequest;
use App\Http\Requests\UpdatePricingRequest;
use App\Http\Resources\Pricing\PricingResource;
use App\Http\Resources\Templates\WithDataResource;
use App\Http\Resources\Templates\WithoutDataResource;
use App\Models\Pricing;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PricingController extends Controller
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

            $query = Pricing::withTrashed();

            // filter
            $filterRules = [
                'pricing_category' => fn($q, $val) => $q->whereIn('pricing_category_id', (array) $val),
            ];

            $filters = $request->except(['limit', 'search']);
            $query   = QueryFilterSearch::applyFilters($query, $filters, $filterRules);

            if ($request->has('search')) {
                $query = QueryFilterSearch::applySearch($query, $request->input('search'), [
                    'name',
                    'pricing_category.name',
                ]);
            }

            $query = QueryFilterSearch::applySortCreatedAt($query);

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
                $data = QueryFilterSearch::formatPaginationCollection($result, PricingResource::class);
            } else {
                $data = [
                    'data' => PricingResource::collection($result),
                    'pagination' => null
                ];
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data paket internet berhasil didapatkan.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('pricing')->error('| Index | - Error function index : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function store(StorePricingRequest $request)
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

            DB::table('pricing_categories')
                ->where('id', $request->pricing_category_id)
                ->lockForUpdate()
                ->first();

            $duplicate = Pricing::where('name', $request->name)
                ->whereNull('deleted_at')
                ->exists();
            if ($duplicate) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_CONFLICT,
                        'DUPLICATE_NAME',
                        'Duplikat Data',
                        "Nama paket internet '{$request->name}' sudah digunakan oleh data lain yang aktif. Silakan gunakan nama lain."
                    ),
                    Response::HTTP_CONFLICT
                );
            }

            $isRecommended = $request->boolean('is_recommended');

            if ($isRecommended) {
                $current = Pricing::query()
                    ->select('id')
                    ->where('pricing_category_id', $request->pricing_category_id)
                    ->where('is_recommended', true)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->first();
                if ($current) {
                    DB::rollBack();
                    return response()->json(
                        new WithoutDataResource(
                            Response::HTTP_CONFLICT,
                            'RECOMMENDED_ALREADY_EXISTS',
                            'Sudah Ada Rekomendasi',
                            'Kategori ini sudah memiliki 1 paket yang direkomendasikan, maksimal 1 paket per kategori.'
                        ),
                        Response::HTTP_CONFLICT
                    );
                }
            }

            Pricing::create([
                'pricing_category_id' => $request->pricing_category_id,
                'name' => $request->name,
                'internet_speed' => $request->internet_speed,
                'price' => $request->price,
                'description' => $request->description,
                'is_recommended' => $isRecommended,
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_CREATED,
                    'SUCCESS_CREATE_DATA',
                    'Berhasil Menyimpan Data',
                    "Data paket internet '{$request->name}' berhasil ditambahkan."
                ),
                Response::HTTP_CREATED
            );
        } catch (QueryException $e) {
            DB::rollBack();
            if ($e->getCode() === '23000') {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_CONFLICT,
                        'RECOMMENDED_ALREADY_EXISTS',
                        'Sudah Ada Rekomendasi',
                        'Kategori ini sudah memiliki 1 paket yang direkomendasikan, maksimal 1 paket per kategori.'
                    ),
                    Response::HTTP_CONFLICT
                );
            }
            Log::channel('pricing')->error('| Store | Error QueryException : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_GET_DATA',
                    'Gagal Mengambil Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('pricing')->error('| Store | - Error function store : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

            $pricing = Pricing::withTrashed()->find($id);
            if (!$pricing) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Harga paket dengan ID tersebut tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    "Detail data paket internet '{$pricing->name}' berhasil didapatkan.",
                    new PricingResource($pricing)
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('pricing')->error('| Detail | - Error function show : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function update(UpdatePricingRequest $request, $id)
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

            $pricing = Pricing::withTrashed()->lockForUpdate()->find($id);
            if (!$pricing) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Harga paket dengan ID tersebut tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $oldCategoryId = (int) $pricing->pricing_category_id;
            $newCategoryId = (int) $request->input('pricing_category_id', $oldCategoryId);
            DB::table('pricing_categories')
                ->whereIn('id', array_unique([$oldCategoryId, $newCategoryId]))
                ->lockForUpdate()
                ->get();

            $duplicate = Pricing::where('name', $request->name)
                ->whereNull('deleted_at')
                ->where('id', '!=', $pricing->id)
                ->exists();
            if ($duplicate) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_CONFLICT,
                        'DUPLICATE_NAME',
                        'Duplikat Data',
                        "Nama paket internet '{$request->name}' sudah digunakan pada data yang sama."
                    ),
                    Response::HTTP_CONFLICT
                );
            }

            $isRecommended = $request->has('is_recommended')
                ? $request->boolean('is_recommended')
                : (bool) $pricing->is_recommended;

            if ($isRecommended) {
                $exists = Pricing::query()
                    ->select('id')
                    ->where('pricing_category_id', $newCategoryId)
                    ->where('is_recommended', true)
                    ->whereNull('deleted_at')
                    ->where('id', '!=', $pricing->id)
                    ->lockForUpdate()
                    ->first();
                if ($exists) {
                    DB::rollBack();
                    return response()->json(
                        new WithoutDataResource(
                            Response::HTTP_CONFLICT,
                            'RECOMMENDED_ALREADY_EXISTS',
                            'Sudah Ada Rekomendasi',
                            'Kategori ini sudah memiliki 1 paket yang direkomendasikan, maksimal 1 paket per kategori.'
                        ),
                        Response::HTTP_CONFLICT
                    );
                }
            }

            $pricing->update([
                'pricing_category_id' => $request->pricing_category_id,
                'name' => $request->name,
                'internet_speed' => $request->internet_speed,
                'price' => $request->price,
                'description' => $request->description,
                'is_recommended' => $request->boolean('is_recommended')
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_DATA',
                    'Berhasil Memperbarui Data',
                    "Data paket internet '{$pricing->name}' berhasil diperbarui."
                ),
                Response::HTTP_OK
            );
        } catch (QueryException $e) {
            DB::rollBack();
            if ($e->getCode() === '23000') {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_CONFLICT,
                        'RECOMMENDED_ALREADY_EXISTS',
                        'Sudah Ada Rekomendasi',
                        'Kategori ini sudah memiliki 1 paket yang direkomendasikan, maksimal 1 paket per kategori.'
                    ),
                    Response::HTTP_CONFLICT
                );
            }
            Log::channel('pricing')->error('| Update | Error QueryException : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_GET_DATA',
                    'Gagal Mengambil Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('pricing')->error('| Update | - Error function update : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

            $pricing = Pricing::find($id);
            if (!$pricing) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Harga paket dengan ID tersebut tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $pricing->delete();

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_DELETE_DATA',
                    'Berhasil Menghapus Data',
                    "Data paket internet '{$pricing->name}' berhasil dihapus."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('pricing')->error('| Destroy | - Error function destroy : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

            $pricing = Pricing::onlyTrashed()->lockForUpdate()->find($id);
            if (!$pricing) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Harga paket dengan ID tersebut tidak ditemukan atau belum dihapus.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            DB::table('pricing_categories')
                ->where('id', $pricing->pricing_category_id)
                ->lockForUpdate()
                ->first();

            // Validasi unik
            $duplicate = Pricing::where('name', $pricing->name)->whereNull('deleted_at')->exists();
            if ($duplicate) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_CONFLICT,
                        'DUPLICATE_NAME',
                        'Duplikat Data',
                        "Nama paket internet '{$pricing->name}' sudah digunakan oleh entri aktif lain. Silakan ubah nama terlebih dahulu sebelum merestore."
                    ),
                    Response::HTTP_CONFLICT
                );
            }

            if ($pricing->is_recommended) {
                $exists = Pricing::query()
                    ->where('pricing_category_id', $pricing->pricing_category_id)
                    ->where('is_recommended', true)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->first();

                if ($exists) {
                    DB::rollBack();
                    return response()->json(
                        new WithoutDataResource(
                            Response::HTTP_CONFLICT,
                            'RECOMMENDED_ALREADY_EXISTS',
                            'Sudah Ada Rekomendasi',
                            'Kategori ini sudah memiliki paket yang direkomendasikan. Nonaktifkan rekomendasi pada paket aktif atau ubah paket ini menjadi tidak direkomendasikan sebelum restore.'
                        ),
                        Response::HTTP_CONFLICT
                    );
                }
            }

            $pricing->restore();

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_RESTORE_DATA',
                    'Berhasil Mengembalikan Data',
                    "Data paket internet '{$pricing->name}' berhasil dikembalikan."
                ),
                Response::HTTP_OK
            );
        } catch (QueryException $e) {
            DB::rollBack();
            if ($e->getCode() === '23000') {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_CONFLICT,
                        'RECOMMENDED_ALREADY_EXISTS',
                        'Sudah Ada Rekomendasi',
                        'Kategori ini sudah memiliki 1 paket yang direkomendasikan, maksimal 1 paket per kategori.'
                    ),
                    Response::HTTP_CONFLICT
                );
            }
            Log::channel('pricing')->error('| Restore | Error QueryException : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_GET_DATA',
                    'Gagal Mengambil Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('pricing')->error('| Restore | - Error function restore : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

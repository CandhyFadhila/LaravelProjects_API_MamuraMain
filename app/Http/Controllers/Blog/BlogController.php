<?php

namespace App\Http\Controllers\Blog;

use App\Helpers\DocumentHelper;
use App\Helpers\QueryFilterSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Http\Resources\Blog\BlogResource;
use App\Http\Resources\Templates\WithDataResource;
use App\Http\Resources\Templates\WithoutDataResource;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
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

            $query = Blog::withTrashed();

            // filter
            $filterRules = [
                'blog_category' => fn($q, $val) => $q->whereIn('blog_category_id', (array) $val),
            ];

            $filters = $request->except(['limit', 'search']);
            $query   = QueryFilterSearch::applyFilters($query, $filters, $filterRules);

            if ($request->has('search')) {
                $query = QueryFilterSearch::applySearch($query, $request->input('search'), [
                    'title',
                    'description',
                    'blog_category.name',
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
                $data = QueryFilterSearch::formatPaginationCollection($result, BlogResource::class);
            } else {
                $data = [
                    'data' => BlogResource::collection($result),
                    'pagination' => null
                ];
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data blog berhasil didapatkan.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('blog')->error('| Index | - Error function index : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function store(StoreBlogRequest $request)
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

            $duplicate = Blog::where('title', $request->title)
                ->where('slug', $request->slug)
                ->whereNull('deleted_at')
                ->exists();
            if ($duplicate) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_CONFLICT,
                        'DUPLICATE_TITLE',
                        'Duplikat Data',
                        "Judul blog '{$request->title}' dan slug blog '{$request->slug}' sudah digunakan oleh data lain yang aktif. Silakan gunakan judul lain."
                    ),
                    Response::HTTP_CONFLICT
                );
            }

            $iconDocumentIds = [];

            if ($request->hasFile('thumbnail_id') && is_array($request->file('thumbnail_id'))) {
                $iconDocumentIds = DocumentHelper::uploadDocuments($request->file('thumbnail_id'));
            }

            Blog::create([
                'thumbnail_id' => $iconDocumentIds ?: null,
                'blog_category_id' => $request->blog_category_id,
                'title' => $request->title,
                'slug' => $request->slug,
                'description' => $request->description,
                'blog_content' => $request->blog_content,
            ]);

            DB::commit();

            $blogCategory = BlogCategory::find($request->blog_category_id);
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_CREATED,
                    'SUCCESS_CREATE_DATA',
                    'Berhasil Menyimpan Data',
                    "Berhasil membuat blog dengan kategori '{$blogCategory->name}' yang berjudul '{$request->title}'."
                ),
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('blog')->error('| Store | - Error function store : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

            $blog = Blog::withTrashed()->find($id);
            if (!$blog) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'Blog dengan ID tersebut tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    "Detail data blog berjudul '{$blog->title}' berhasil didapatkan.",
                    new BlogResource($blog)
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('blog')->error('| Detail | - Error function show : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function update(UpdateBlogRequest $request, $id)
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

            $blog = Blog::withTrashed()->find($id);
            if (!$blog) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'blog dengan ID tersebut tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = $request->validated();

            $duplicate = Blog::where('title', $request->title)
                ->where('slug', $request->slug)
                ->where('id', '!=', $blog->id)
                ->whereNull('deleted_at')
                ->exists();
            if ($duplicate) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_CONFLICT,
                        'DUPLICATE_TITLE',
                        'Duplikat Data',
                        "Judul blog '{$request->title}' dan slug blog '{$request->slug}' sudah digunakan oleh data lain yang aktif. Silakan gunakan judul lain."
                    ),
                    Response::HTTP_CONFLICT
                );
            }

            $existingDocumentIds = $blog->thumbnail_id ?? [];
            $deleteIds = $data['delete_thumbnail_ids'] ?? [];
            $newUploads = $request->file('thumbnail_id') ?? [];

            // ✅ Safety: jika delete kosong & dokumen baru full, asumsikan ingin overwrite semua
            if (empty($deleteIds) && count($newUploads) === 5 && !empty($existingDocumentIds)) {
                $deleteIds = $existingDocumentIds;
                $data['delete_thumbnail_ids'] = $deleteIds;
            }

            // ✅ Validasi jumlah total dokumen (existing - delete + new) ≤ 5
            $remainingDocs = array_values(array_diff($existingDocumentIds, $deleteIds));
            $totalAfter = count($remainingDocs) + count($newUploads);
            if ($totalAfter > 5) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_BAD_REQUEST,
                        'TOO_MANY_DOCUMENTS',
                        'Terlalu Banyak Dokumen',
                        "Jumlah total thumbnail setelah update melebihi batas maksimum (maksimal 5)."
                    ),
                    Response::HTTP_BAD_REQUEST
                );
            }


            // ✅ Hapus dokumen lama jika ada
            if (!empty($deleteIds)) {
                DocumentHelper::deleteDocuments($deleteIds);
                $existingDocumentIds = array_values(array_diff($existingDocumentIds, $deleteIds));
            }

            // ✅ Upload dokumen baru
            $newDocumentIds = [];
            if (!empty($newUploads)) {
                $newDocumentIds = DocumentHelper::uploadDocuments($newUploads);
            }

            $finalThumbnails = array_merge($existingDocumentIds, $newDocumentIds);

            $blog->update([
                'thumbnail_id' => $finalThumbnails ?: null,
                'blog_category_id' => $request->blog_category_id,
                'title' => $request->title,
                'slug' => $request->slug,
                'description' => $request->description,
                'blog_content' => $request->blog_content,
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_DATA',
                    'Berhasil Memperbarui Data',
                    "Data blog dengan judul '{$blog->title}' berhasil diperbarui."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('blog')->error('| Update | - Error function update : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

            $blog = Blog::find($id);
            if (!$blog) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'blog dengan ID tersebut tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $blog->delete();

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_DELETE_DATA',
                    'Berhasil Menghapus Data',
                    "Data blog dengan judul '{$blog->title}' berhasil dihapus."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('blog')->error('| Destroy | - Error function destroy : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

            $blog = Blog::onlyTrashed()->find($id);
            if (!$blog) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Data Tidak Ditemukan',
                        'blog dengan ID tersebut tidak ditemukan atau belum dihapus.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            // Validasi unik
            $duplicate = Blog::where('title', $blog->title)
                ->where('slug', $blog->slug)
                ->whereNull('deleted_at')
                ->exists();
            if ($duplicate) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_CONFLICT,
                        'DUPLICATE_TITLE',
                        'Duplikat Data',
                        "Judul blog '{$blog->title}' dan slug blog '{$blog->slug}' sudah digunakan oleh data lain yang aktif. Silakan gunakan judul lain."
                    ),
                    Response::HTTP_CONFLICT
                );
            }

            $blog->restore();

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_RESTORE_DATA',
                    'Berhasil Mengembalikan Data',
                    "Blog dengan judul '{$blog->title}' berhasil dikembalikan."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('blog')->error('| Restore | - Error function restore : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function publicIndex(Request $request)
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

            $query = Blog::withoutTrashed();

            // filter
            $filterRules = [
                'blog_category' => fn($q, $val) => $q->whereIn('blog_category_id', (array) $val),
            ];

            $filters = $request->except(['limit', 'search']);
            $query   = QueryFilterSearch::applyFilters($query, $filters, $filterRules);

            if ($request->has('search')) {
                $query = QueryFilterSearch::applySearch($query, $request->input('search'), [
                    'title',
                    'description',
                    'blog_category.name',
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
                $data = QueryFilterSearch::formatPaginationCollection($result, BlogResource::class);
            } else {
                $data = [
                    'data' => BlogResource::collection($result),
                    'pagination' => null
                ];
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data blog berhasil didapatkan.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('blog')->error('| Index | - Error function publicIndex : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

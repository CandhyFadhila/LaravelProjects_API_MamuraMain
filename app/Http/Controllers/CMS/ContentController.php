<?php

namespace App\Http\Controllers\CMS;

use App\Helpers\DocumentHelper;
use App\Helpers\QueryFilterSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContentCMSRequest;
use App\Http\Requests\UpdateContentCMSRequest;
use App\Http\Resources\CMS\ContentResource;
use App\Http\Resources\Templates\WithDataResource;
use App\Http\Resources\Templates\WithoutDataResource;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ContentController extends Controller
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

            $query = Content::withTrashed();

            // filter
            $filterRules = [
                'content_type' => fn($q, $val) => $q->whereIn('content_type_id', (array) $val),
            ];

            $filters = $request->except(['limit', 'search']);
            $query   = QueryFilterSearch::applyFilters($query, $filters, $filterRules);

            if ($request->has('search')) {
                $query = QueryFilterSearch::applySearch($query, $request->input('search'), [
                    'content_type.name',
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
                $collection = ContentResource::collection($result)->keyBy('id');
                $data = QueryFilterSearch::formatPaginationCollection($collection, null);
            } else {
                $formattedData = ContentResource::collection($result)->keyBy('id');

                $data = [
                    'data' => $formattedData,
                    'pagination' => null
                ];
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Data konten berhasil didapatkan.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('content')->error('| Index | - Error function index : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function store(StoreContentCMSRequest $request)
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

            // $duplicate = Content::where('title', $request->title)
            //     ->whereNull('deleted_at')
            //     ->exists();
            // if ($duplicate) {
            //     return response()->json(
            //         new WithoutDataResource(
            //             Response::HTTP_CONFLICT,
            //             'DUPLICATE_TITLE',
            //             'Duplikat Data',
            //             "Judul blog '{$request->title}' sudah digunakan oleh data lain yang aktif. Silakan gunakan judul lain."
            //         ),
            //         Response::HTTP_CONFLICT
            //     );
            // }

            $contentFileIds = [];

            if ($request->hasFile('content_file_id') && is_array($request->file('content_file_id'))) {
                $contentFileIds = DocumentHelper::uploadDocuments($request->file('content_file_id'));
            }

            $contentValue = $request->content;

            $contentType = ContentType::find($request->content_type_id);

            if ($contentType && !in_array(strtolower($contentType->name), ['text', 'tautan'])) {
                if (!empty($contentFileIds)) {
                    $documents = Document::whereIn('id', $contentFileIds)->pluck('file_url')->toArray();

                    if (count($documents) === 1) {
                        $contentValue = $documents[0];
                    } else {
                        $contentValue = json_encode($documents);
                    }
                } else {
                    $contentValue = null;
                }
            }

            Content::create([
                'content_file_id' => $contentFileIds ?: null,
                'content_type_id' => $request->content_type_id,
                'content' => $contentValue
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_CREATED,
                    'SUCCESS_CREATE_DATA',
                    'Berhasil Menyimpan Data',
                    "Berhasil membuat konten baru dengan tipe konten '{$contentType->name}'."
                ),
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('content')->error('| Store | - Error function store : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

            $content = Content::withTrashed()->find($id);
            if (!$content) {
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
                    "Detail konten yang dipilih berhasil didapatkan.",
                    new ContentResource($content)
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('content')->error('| Detail | - Error function show : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function update(UpdateContentCMSRequest $request, $id)
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

            $content = Content::withTrashed()->find($id);
            if (!$content) {
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

            $existingDocumentIds = $content->content_file_id ?? [];
            $deleteIds = $data['delete_content_file_ids'] ?? [];
            $newUploads = $request->file('content_file_id') ?? [];

            // ✅ Safety: jika delete kosong & dokumen baru full, asumsikan ingin overwrite semua
            if (empty($deleteIds) && count($newUploads) === 5 && !empty($existingDocumentIds)) {
                $deleteIds = $existingDocumentIds;
                $data['delete_content_file_ids'] = $deleteIds;
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
                        "Jumlah total konten setelah update melebihi batas maksimum (maksimal 5)."
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

            $contentFileIds = array_merge($existingDocumentIds, $newDocumentIds);

            $contentValue = $request->content;

            $contentType = ContentType::find($request->content_type_id);

            // Cek kalau content type bukan text atau berupa file
            if ($contentType && !in_array(strtolower($contentType->name), ['text', 'tautan'])) {
                if (!empty($contentFileIds)) {
                    $documents = Document::whereIn('id', $contentFileIds)->pluck('file_url')->toArray();

                    if (count($documents) === 1) {
                        $contentValue = $documents[0];
                    } else {
                        $contentValue = json_encode($documents);
                    }
                } else {
                    $contentValue = null;
                }
            }

            $content->update([
                'content_file_id' => $contentFileIds ?: null,
                'content_type_id' => $request->content_type_id,
                'content' => $contentValue,
            ]);

            DB::commit();
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_UPDATE_DATA',
                    'Berhasil Memperbarui Data',
                    "Data konten yang dipilih berhasil diperbarui."
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('content')->error('| Update | - Error function update : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

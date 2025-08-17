<?php

namespace App\Http\Controllers\Public;

use App\Helpers\DocumentHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Blog\BlogCategoryResource;
use App\Http\Resources\Blog\BlogResource;
use App\Http\Resources\Carrier\CarrierCategoryResource;
use App\Http\Resources\Carrier\CarrierResource;
use App\Http\Resources\Carrier\EmployeeStatusResource;
use App\Http\Resources\Carrier\JobLocationResource;
use App\Http\Resources\CMS\ContentResource;
use App\Http\Resources\CMS\ContentTypeResource;
use App\Http\Resources\CoverageArea\SupportedCityResource;
use App\Http\Resources\CoverageArea\SupportedProvinceResource;
use App\Http\Resources\FAQ\FaqResource;
use App\Http\Resources\Pricing\PricingCategoryResource;
use App\Http\Resources\Pricing\PricingResource;
use App\Http\Resources\Promo\PromoResource;
use App\Models\BlogCategory;
use App\Http\Resources\Templates\WithDataResource;
use App\Http\Resources\Templates\WithoutDataResource;
use App\Models\Blog;
use App\Models\Carrier;
use App\Models\CarrierCategory;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Document;
use App\Models\EmployeeStatus;
use App\Models\Faq;
use App\Models\JobLocation;
use App\Models\PricingCategory;
use App\Models\Promo;
use App\Models\SupportedCity;
use App\Models\SupportedProvince;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PublicRequestController extends Controller
{
    public function getBlogCategory()
    {
        try {
            $blogCategory = BlogCategory::all();
            if ($blogCategory->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data kategori blog tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = BlogCategoryResource::collection($blogCategory)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data kategori blog.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getBlogCategory : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getContentType()
    {
        try {
            $contentType = ContentType::all();
            if ($contentType->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data tipe konten tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = ContentTypeResource::collection($contentType)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data tipe konten.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getContentType : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getCarrierCategory()
    {
        try {
            $carrierCategory = CarrierCategory::all();
            if ($carrierCategory->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data kategori karir tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = CarrierCategoryResource::collection($carrierCategory)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data kategori karir.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getCarrierCategory : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getEmployeeStatus()
    {
        try {
            $employeeStatus = EmployeeStatus::all();
            if ($employeeStatus->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data status karyawan tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = EmployeeStatusResource::collection($employeeStatus)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data status karyawan.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getEmployeeStatus : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getJobLocation()
    {
        try {
            $jobLocation = JobLocation::all();
            if ($jobLocation->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data lokasi penempatan pekerjaan tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = JobLocationResource::collection($jobLocation)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data lokasi penempatan pekerjaan.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getJobLocation : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getSupportedCity()
    {
        try {
            $supportedCity = SupportedCity::all();
            if ($supportedCity->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data kota yang disupport tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = SupportedCityResource::collection($supportedCity)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data kota yang disupport.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getSupportedCity : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getSupportedProvince()
    {
        try {
            $supportedProvince = SupportedProvince::all();
            if ($supportedProvince->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data kota yang disupport tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = SupportedProvinceResource::collection($supportedProvince)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data kota yang disupport.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getSupportedProvince : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getPricingCategory()
    {
        try {
            $pricingCategory = PricingCategory::all();
            if ($pricingCategory->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data kategori harga paket internet tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = PricingCategoryResource::collection($pricingCategory)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data kategori harga paket internet.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getPricingCategory : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    // get content
    public function getAllContent()
    {
        try {
            $content = Content::all();
            if ($content->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data konten tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $formattedData = ContentResource::collection($content)
                ->keyBy('id')
                ->map(function ($item) {
                    return collect($item)->except(['created_at', 'updated_at', 'deleted_at']);
                });

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data konten.',
                    $formattedData
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getAllContent : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getContentbyId($id)
    {
        try {
            $content = Content::find($id);
            if (!$content) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data konten tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = collect(new ContentResource($content))->except(['created_at', 'updated_at', 'deleted_at']);

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data konten berdasarkan id.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getContentbyId : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getContentHero()
    {
        try {
            $content = Content::whereIn('id', [1, 2, 3, 4, 5])->get();
            if ($content->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data konten tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $formattedData = ContentResource::collection($content)->keyBy('id')
                ->map(function ($item) {
                    return collect($item)->except(['created_at', 'updated_at', 'deleted_at']);
                });

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data konten didalam hero.',
                    $formattedData
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getContentHero : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getPricingbyCategory()
    {
        try {
            $pricingCategory = PricingCategory::with('pricings')->get();
            if ($pricingCategory->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data kategori harga paket internet tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            // ✅ Transformasi ke format: "CategoryName" => [ array of pricing ]
            $data = [];
            foreach ($pricingCategory as $category) {
                $data[$category->name] = PricingResource::collection($category->pricings)->toArray(request());
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data kategori harga paket internet.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getPricingbyCategory : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getPromo()
    {
        try {
            $promo = Promo::where('promo_end', '>=', today()->endOfDay())
                ->get();
            if ($promo->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data promo tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = PromoResource::collection($promo)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data promo.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getPromo : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getFaq()
    {
        try {
            $pricingCategory = Faq::all();
            if ($pricingCategory->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data faq tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = FaqResource::collection($pricingCategory)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data faq.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getFaq : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getBlog()
    {
        try {
            $blog = Blog::all();
            if ($blog->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data blog tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            // Ambil blog acak selain ID yang dikirim
            $blog = Blog::query()
                ->latest('created_at')
                ->limit(5)                      // jumlah item
                ->get();

            $data = BlogResource::collection($blog);

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data blog.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getBlog : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getBlogbySlug($slug)
    {
        try {
            $blog = Blog::where('slug', $slug)->first();
            if (!$blog) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data blog tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = collect(new BlogResource($blog));

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data blog berdasarkan slug.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getBlogbySlug : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getBlogNews($id)
    {
        try {
            $blog = Blog::whereKey($id)->exists();
            if (!$blog) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data blog tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            // Ambil blog acak selain ID yang dikirim
            $blog = Blog::query()
                ->where('id', '!=', $id)        // exclude id
                ->latest('created_at')
                ->limit(5)                      // jumlah item
                ->get();

            $data = BlogResource::collection($blog);

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data blog random selain id yang diberikan.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getBlogNews : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    public function getCarrier()
    {
        try {
            $karir = Carrier::all();
            if ($karir->isEmpty()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_NOT_FOUND,
                        'DATA_NOT_FOUND',
                        'Tidak Ada Data',
                        'Data karir tidak ditemukan.',
                    ),
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = CarrierResource::collection($karir)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data karir.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getCarrier : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    // Fungsi untuk get all data content
    // contents
    // promo
    // pricing
    // faqs
    // blogs
    // career
    public function getPublicAllData()
    {
        try {
            // ✅ Contents
            $contentsAssoc = Content::query()
                ->select('id', 'content')
                ->orderBy('id')
                ->get()
                ->mapWithKeys(fn($row) => [(string) $row->id => $row->content]);

            $contentData = $contentsAssoc->isEmpty()
                ? (object) []
                : (object) $contentsAssoc->toArray();

            // ✅ Promo
            $promos = Promo::where('promo_end', '>=', today()->endOfDay())
                ->get();
            $promoData = $promos->isEmpty()
                ? []
                : PromoResource::collection($promos)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            // ✅ Pricing (grouped by category name)
            $pricingCategories = PricingCategory::with('pricings')->get();
            $pricingData = [];
            if (!$pricingCategories->isEmpty()) {
                foreach ($pricingCategories as $category) {
                    $pricingData[$category->name] = PricingResource::collection($category->pricings)->toArray(request());
                }
            }

            // ✅ Faqs
            $faqs = Faq::all();
            $faqData = $faqs->isEmpty()
                ? []
                : FaqResource::collection($faqs)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            // ✅ Blogs (hanya ID 1-5)
            $blogs = Blog::whereIn('id', [1, 2, 3, 4, 5])->get();
            $blogData = $blogs->isEmpty()
                ? []
                : BlogResource::collection($blogs)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            // ✅ Careers
            $careers = Carrier::all();
            $careerData = $careers->isEmpty()
                ? []
                : CarrierResource::collection($careers)
                ->map(fn($item) => collect($item)->except(['created_at', 'updated_at', 'deleted_at']));

            // ✅ Gabungkan semua
            $result = [
                'contents' => $contentData,
                'promo'    => $promoData,
                'pricing'  => $pricingData,
                'faqs'     => $faqData,
                'blogs'    => $blogData,
                'career'   => $careerData,
            ];

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data kategori harga paket internet.',
                    $result
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('public_request')->error('| Public Request | - Error function getPublicAllData : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

    // intern BE
    // 1. Upload 3 gambar
    public function uploadInternalImage(Request $request)
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

            // Validasi: maksimal 3 gambar, format dan ukuran
            $validator = Validator::make(
                [
                    'intern_image_be'   => 'required|array|max:5',
                    'intern_image_be.*' => 'required|mimes:jpg,jpeg,png|max:10240',
                    'delete_document_ids' => 'nullable|array',
                    'delete_document_ids.*' => 'nullable|integer',
                ],
                [
                    'intern_image_be.required' => 'Gambar tidak boleh kosong.',
                    'intern_image_be.array' => 'Gambar harus berupa array.',
                    'intern_image_be.max' => 'Maksimal gambar yang diunggah adalah 5 gambar.',
                    'intern_image_be.*.required' => 'Gambar tidak boleh kosong.',
                    'intern_image_be.*.mimes' => 'Gambar hanya boleh berupa JPG, JPEG, dan PNG.',
                    'intern_image_be.*.max' => 'Ukuran gambar maksimal 10MB.',
                    'delete_document_ids.array' => 'Format dokumen yang dihapus harus berupa array.',
                    'delete_document_ids.*.integer' => 'ID dokumen yang dihapus harus berupa angka.',
                ]
            );

            if ($validator->fails()) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_BAD_REQUEST,
                        'FAILED_VALIDATION',
                        'Format Data Tidak Sesuai Ketentuan',
                        $validator->errors()->first()
                    ),
                    Response::HTTP_BAD_REQUEST
                );
            }

            // --- Normalisasi input terverifikasi ---
            $files = $request->file('intern_image_be', []);
            $files = is_array($files) ? $files : [$files];
            $files = array_values(array_filter($files)); // buang null jika ada

            $deletedRaw = $request->input('delete_document_ids', []);
            $deletedRaw = is_array($deletedRaw) ? $deletedRaw : [$deletedRaw];
            $deletedDocumentIds = array_values(array_map(
                'intval',
                array_filter($deletedRaw, fn($v) => $v !== null && $v !== '' && is_numeric($v))
            ));

            // --- Hapus dokumen lama (jika ada) ---
            if (!empty($deletedDocumentIds)) {
                DocumentHelper::deleteDocuments($deletedDocumentIds);
            }

            // --- Upload dokumen baru (jika ada) ---
            $documentIds = [];
            if (!empty($files)) {
                $documentIds = DocumentHelper::uploadDocuments($files);
                if (empty($documentIds)) {
                    DB::rollBack();
                    return response()->json(
                        new WithoutDataResource(
                            Response::HTTP_INTERNAL_SERVER_ERROR,
                            'UPLOAD_FAILED',
                            'Gagal Mengunggah Gambar',
                            'Gagal mengunggah gambar ke server penyimpanan dokumen.'
                        ),
                        Response::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            }

            DB::commit();

            // --- Ambil trio id, file_id, file_url utk yang baru diupload ---
            $uploadedDocs = [];
            if (!empty($documentIds)) {
                $uploadedDocs = Document::whereIn('id', $documentIds)
                    ->get(['id', 'file_id', 'file_url'])
                    ->map(fn($d) => [
                        'id'       => $d->id,
                        'file_id'  => $d->file_id,
                        'file_url' => $d->file_url,
                    ])
                    ->values()
                    ->toArray();
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_CREATED,
                    'SUCCESS_UPLOAD_IMAGE',
                    'Berhasil Mengunggah Gambar',
                    'Berhasil mengunggah hingga 3 gambar untuk kebutuhan internal BE.',
                    [
                        'deleted_document_ids' => $deletedDocumentIds,
                        'uploaded_documents'   => $uploadedDocs,
                    ]
                ),
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('public_request_internal_BE')->error('| Store | - Error function uploadInternalImage : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
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

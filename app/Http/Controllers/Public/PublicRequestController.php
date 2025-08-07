<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Blog\BlogCategoryResource;
use App\Http\Resources\Carrier\CarrierCategoryResource;
use App\Http\Resources\Carrier\EmployeeStatusResource;
use App\Http\Resources\Carrier\JobLocationResource;
use App\Http\Resources\CMS\ContentResource;
use App\Http\Resources\CMS\ContentTypeResource;
use App\Http\Resources\CoverageArea\SupportedCityResource;
use App\Http\Resources\CoverageArea\SupportedProvinceResource;
use App\Models\BlogCategory;
use App\Http\Resources\Templates\WithDataResource;
use App\Http\Resources\Templates\WithoutDataResource;
use App\Models\CarrierCategory;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\EmployeeStatus;
use App\Models\JobLocation;
use App\Models\SupportedCity;
use App\Models\SupportedProvince;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

// TODO: Tambahkan pengecualian untuk hidden timestamp
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

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data kategori blog.',
                    BlogCategoryResource::collection($blogCategory)
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

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data tipe konten.',
                    ContentTypeResource::collection($contentType)
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

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data kategori karir.',
                    CarrierCategoryResource::collection($carrierCategory)
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

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data status karyawan.',
                    EmployeeStatusResource::collection($employeeStatus)
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

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data lokasi penempatan pekerjaan.',
                    JobLocationResource::collection($jobLocation)
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

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data kota yang disupport.',
                    SupportedCityResource::collection($supportedCity)
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

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data kota yang disupport.',
                    SupportedProvinceResource::collection($supportedProvince)
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

            $formattedData = ContentResource::collection($content)->keyBy('id');

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

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Berhasil mengambil data konten.',
                    new ContentResource($content)
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
            $content = Content::whereIn('id', [2, 3, 4, 5])->get();
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

            $formattedData = ContentResource::collection($content)->keyBy('id');

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

    // TODO: Supported city

    // TODO: Supported province

    // TODO: Pricing category
}

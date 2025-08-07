<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Blog\BlogCategoryController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Carrier\CarrierCategoryController;
use App\Http\Controllers\Carrier\CarrierController;
use App\Http\Controllers\Carrier\EmployeeStatusController;
use App\Http\Controllers\Carrier\JobApplicationController;
use App\Http\Controllers\Carrier\JobLocationController;
use App\Http\Controllers\CMS\ContentController;
use App\Http\Controllers\Contact\InquiryController;
use App\Http\Controllers\CoverageArea\SupportedCityController;
use App\Http\Controllers\CoverageArea\SupportedProvinceController;
use App\Http\Controllers\FAQ\FaqController;
use App\Http\Controllers\Pricing\PricingCategoryController;
use App\Http\Controllers\Pricing\PricingController;
use App\Http\Controllers\Public\PublicRequestController;
use Illuminate\Support\Facades\Route;

// TODO: middleware ketika user login ke auth admin
Route::middleware(['custom.throttle:5,1'])->group(function () {

    // <!-- Test Throttle pakek ini -->
    // Route::get('/test-throttle', function () {
    //     return response()->json(['message' => 'OK']);
    // });
    // <!-- Test Throttle pakek ini -->

    Route::post('/signup', [RegisterController::class, 'signUp']);
    Route::post('/signin', [LoginController::class, 'signIn']);
    Route::post('/signup-verify-otp', [RegisterController::class, 'signUpVerifyOTP']);
    Route::post('/send-otp', [ForgotPasswordController::class, 'sendOTP']);
    Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOTP']);
    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);
});

Route::middleware(['auth:sanctum', 'custom.throttle:20,1'])->group(function () {
    Route::get('/logout', [LoginController::class, 'logout'])->middleware('web');
    Route::get('/user-info', [LoginController::class, 'getUserInfo']);

    Route::group(['prefix' => 'mamura'], function () {
        Route::group(['prefix' => 'public-request'], function () {
            // all supported city
            // all supported province
            // all pricing category
            Route::get('/get-blog-category', [PublicRequestController::class, 'getBlogCategory']);
            Route::get('/get-content-type', [PublicRequestController::class, 'getContentType']);
            Route::get('/get-carrier-category', [PublicRequestController::class, 'getCarrierCategory']);
            Route::get('/get-employee-status', [PublicRequestController::class, 'getEmployeeStatus']);
            Route::get('/get-job-location', [PublicRequestController::class, 'getJobLocation']);
            Route::get('/get-supported-city', [PublicRequestController::class, 'getSupportedCity']);
            Route::get('/get-supported-province', [PublicRequestController::class, 'getSupportedProvince']);
            Route::get('/get-pricing-category', [PublicRequestController::class, 'getPricingCategory']);
            Route::get('/get-pricing-by-category', [PublicRequestController::class, 'getPricingbyCategory']);
            Route::get('/get-faq', [PublicRequestController::class, 'getFaq']);
            Route::get('/get-blog', [PublicRequestController::class, 'getBlog']);
            Route::get('/get-all-content', [PublicRequestController::class, 'getAllContent']);
            Route::get('/get-content/{id}', [PublicRequestController::class, 'getContentbyId']);
            Route::get('/get-content-hero', [PublicRequestController::class, 'getContentHero']);
        });

        Route::group(['prefix' => 'admin', 'middleware' => ['verified.role:admin']], function () {
            Route::group(['prefix' => 'dashboard'], function () {
                // route dashboard
            });

            // Modul
            Route::apiResource('/blog', BlogController::class);
            Route::post('/blog/{id}/restore', [BlogController::class, 'restore']);

            Route::apiResource('/content', ContentController::class);

            Route::apiResource('/carrier', CarrierController::class);
            Route::post('/carrier/{id}/restore', [CarrierController::class, 'restore']);

            Route::apiResource('/job-application', JobApplicationController::class);
            Route::post('/job-application/{id}/restore', [JobApplicationController::class, 'restore']);

            Route::apiResource('/inquiry', InquiryController::class);
            Route::post('/inquiry/{id}/restore', [InquiryController::class, 'restore']);

            Route::apiResource('/pricing', PricingController::class);
            Route::post('/pricing/{id}/restore', [PricingController::class, 'restore']);

            Route::apiResource('/faq', FaqController::class);
            Route::post('/faq/{id}/restore', [FaqController::class, 'restore']);

            Route::group(['prefix' => 'master-data'], function () {
                Route::apiResource('/blog-category', BlogCategoryController::class);
                Route::post('/blog-category/{id}/restore', [BlogCategoryController::class, 'restore']);

                Route::apiResource('/carrier-category', CarrierCategoryController::class);
                Route::post('/carrier-category/{id}/restore', [CarrierCategoryController::class, 'restore']);

                Route::apiResource('/employee-status', EmployeeStatusController::class);
                Route::post('/employee-status/{id}/restore', [EmployeeStatusController::class, 'restore']);

                Route::apiResource('/job-location', JobLocationController::class);
                Route::post('/job-location/{id}/restore', [JobLocationController::class, 'restore']);

                Route::apiResource('/supported-city', SupportedCityController::class);
                Route::post('/supported-city/{id}/restore', [SupportedCityController::class, 'restore']);

                Route::apiResource('/supported-province', SupportedProvinceController::class);
                Route::post('/supported-province/{id}/restore', [SupportedProvinceController::class, 'restore']);
            });

            Route::group(['prefix' => 'setting'], function () {
                // route setting
            });
        });

        Route::group(['middleware' => ['verified.role:[users, admin]']], function () {
            Route::post('/create-inquiry', [InquiryController::class, 'publicCreate']);

            
        });
    });
});

<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Blog\BlogCategoryController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\CMS\ContentController;
use App\Http\Controllers\CMS\ContentTypeController;
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
            // route public request
        });

        Route::group(['prefix' => 'admin', 'middleware' => ['verified.role:admin']], function () {
            Route::group(['prefix' => 'dashboard'], function () {
                // route dashboard
            });

            Route::group(['prefix' => 'master-data'], function () {
                Route::apiResource('/blog-category', BlogCategoryController::class);
                Route::post('/blog-category/{id}/restore', [BlogCategoryController::class, 'restore']);

                Route::apiResource('/blog', BlogController::class);
                Route::post('/blog/{id}/restore', [BlogController::class, 'restore']);

                // TODO: Tinggal testing
                Route::apiResource('/content-type', ContentTypeController::class);
                Route::post('/content-type/{id}/restore', [ContentTypeController::class, 'restore']);

                Route::apiResource('/cms', ContentController::class);
                Route::post('/cms/{id}/restore', [ContentController::class, 'restore']);
            });

            Route::group(['prefix' => 'setting'], function () {
                // route setting
            });
        });

        // Route::group(['middleware' => ['verified.role:users']], function () {
        //     // route module user
        // });
    });
});

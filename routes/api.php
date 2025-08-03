<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// TODO: middleware ketika user login ke auth admin
Route::middleware(['custom.throttle:5,1'])->group(function () {
    // untuk auth
});

Route::middleware(['auth:sanctum', 'custom.throttle:60,1'])->group(function () {
    // logout
    // user info

    Route::group(['prefix' => 'mamura'], function () {
        Route::group(['prefix' => 'public-request'], function () {
            // route public request
        });

        Route::group(['prefix' => 'admin', 'middleware' => ['verified.role:admin']], function () {
            // route module master data
        });
        Route::group(['middleware' => ['verified.role:users']], function () {
            // route module user
        });
    });
});

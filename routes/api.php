<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CspController;
use App\Http\Controllers\TestCspController;

Route::group(['prefix' => 'test', 'middleware' => 'api'], function () {
    if (app()->environment(['local', 'testing', 'staging'])) {
        Route::post('/populate', [TestCspController::class, 'populate']);
        Route::post('/reset', [TestCspController::class, 'reset']);
    }
});

Route::post('/csp-report', [CspController::class, 'store']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

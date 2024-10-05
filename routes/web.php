<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/admin/reports', [AdminController::class, 'index'])->name('reports.index');
Route::get('/admin/reports/{domain}', [AdminController::class, 'show'])->name('reports.show');

Route::get('/', function () {
    return view('welcome');
});

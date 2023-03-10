<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResponseController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [ReportController::class, "index"])->name('home');
Route::post('/store', [ReportController::class, "store"])->name('store');
Route::post('/auth', [ReportController::class, "auth"])->name('auth');


Route::get('/login', function () {
    return view('login');
})->name('login');



Route::get('/logout', [ReportController::class, "logout"])->name('logout');


Route::middleware(['isLogin', 'CekRole:petugas'])->group(function() {
    Route::get('/data/petugas', [ReportController::class, "dataPetugas"])->name('data.petugas');
    // menampilkan form tambahan atau ubah form response
    Route::get('/response/edit/{report_id}', [ResponseController::class, "edit"])->name('response.edit');
    // kirim data response. menggunakan patch, karena dia bisa berupa tambah data ataau update data
    Route::patch('/response/update/{report_id}', [ResponseController::class, "update"])->name('response.update');
});

Route::middleware(['isLogin', 'CekRole:admin,petugas'])->group(function() {
    Route::get('/logout', [ReportController::class, "logout"])->name('logout');
});


// route yang dapat diakses setelah login dan role nya admin
Route::middleware(['isLogin', 'CekRole:admin'])->group(function() {
    Route::get('/data', [ReportController::class, "data"])->name('data');
    Route::get('/destroy/{id}', [ReportController::class, "delete"])->name('destroy');
    Route::get('/export/pdf', [ReportController::class, "exportPDF"])->name('export-pdf');
    Route::get('/print/pdf/{id}', [ReportController::class, "printPDF"])->name('print-PDF');
    Route::get('/export/excel', [ReportController::class, "exportExcel"])->name('export.excel');
});


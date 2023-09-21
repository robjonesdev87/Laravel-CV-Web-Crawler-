<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Auth::routes();

Route::controller(App\Http\Controllers\HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::post('/flush-redis', 'flushRedis')->name('flushRedis');
});

Route::controller(App\Http\Controllers\CrawlController::class)->group(function () {
    Route::post('/get-queue-data', 'getQueueData')->name('getQueueData');
    Route::post('/validate', 'validateURL')->name('validateURL');
});




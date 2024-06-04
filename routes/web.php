<?php

use App\Http\Controllers\XmlController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/form', [XmlController::class, 'index'])->name('form');
Route::post('/upload', [XmlController::class, 'upload'])->name('upload');
Route::get('/xml/{id}', [XmlController::class, 'show'])->name('xml.show');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
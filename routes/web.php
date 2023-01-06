<?php

use App\Http\Controllers\CompressImageController;
use Illuminate\Support\Facades\Route;

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

Route::get('/',[CompressImageController::class,"index"]);
Route::post('/upload',[CompressImageController::class,"compressImg"])->name("upload");

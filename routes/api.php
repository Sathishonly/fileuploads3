<?php

use App\Http\Controllers\fileUploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//api for s3
Route::post('fileupload', [fileUploadController::class, 'fileupload']);
Route::get('getfileurl', [fileUploadController::class, 'getfileurl']);
Route::post('multiplefileupload', [fileUploadController::class, 'multiplefileupload']);
Route::delete('deletefile', [fileUploadController::class, 'deletefile']);
Route::post('deletemultiplefiles', [fileUploadController::class, 'deletemultiplefiles']);
//for temporaryUrl
Route::get('getfiletemurl', [fileUploadController::class, 'getfiletemurl']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

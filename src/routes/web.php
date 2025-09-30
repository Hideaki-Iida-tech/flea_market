<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;

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

/*Route::get('/', function () {
    return view('items/index');
});*/
/*Route::get('/register', function () {
    return view('auth/register');
});*/
/*Route::get('/login', function () {
    return view('auth/login');
});*/

Route::middleware('auth')->group(function () {

    Route::get('/mypage/profile', [ProfileController::class, 'edit']);
    Route::post('/mypage/profile', [ProfileController::class, 'update']);
    Route::get('/mypage', function () {
        return view('profile/index');
    });
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->where('item_id', '[0-9]+');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->where('item_id', '[0-9]+');
    Route::post('/purchase/{item_id}/payment/draft', [PurchaseController::class, 'savePaymentDraft'])->where('item_id', '[0-9]+');
    Route::get('/sell', [ItemController::class, 'create']);
    Route::post('sell', [ItemController::class, 'store']);
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'addressIndex'])->where('item_id', '[0-9]+');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'addressUpdate'])->where('item_id', '[0-9]+');
    Route::post('/item/{item_id}/comment', [ItemController::class, 'commentCreate'])->where('item_id', '[0-9]+');
    Route::post('/item/{item_id}/like', [ItemController::class, 'toggle'])->where('item_id', '[0-9]+');
});
Route::get('/item/{item_id}', [ItemController::class, 'show'])->where('item_id', '[0-9]+');



/*Route::get('/mypage', function () {
    return view('profile/index');
});*/
/*Route::get('/mypage/profile', function () {
    return view('profile/edit');
});*/
Route::get('/verify-email', function () {
    return view('auth/verify-email');
});
Route::get('/', [ItemController::class, 'index']);

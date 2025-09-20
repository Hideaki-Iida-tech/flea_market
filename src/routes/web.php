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
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index']);
    Route::get('/sell', function () {
        return view('items/create');
    });
    Route::get('/purchase/address/{item_id}', function () {
        return view('address/edit');
    });
});
Route::get('/item/{item_id}', [ItemController::class, 'show']);



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

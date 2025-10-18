<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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

Route::middleware('auth', 'verified')->group(function () {

    Route::get('/mypage/profile', [ProfileController::class, 'edit']);
    Route::post('/mypage/profile', [ProfileController::class, 'update']);
    Route::get('/mypage', [ProfileController::class, 'index']);
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->where('item_id', '[0-9]+');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->where('item_id', '[0-9]+');
    Route::post('/purchase/{item_id}/payment/draft', [PurchaseController::class, 'savePaymentDraft'])->where('item_id', '[0-9]+');
    Route::get('/sell', [ItemController::class, 'create']);
    Route::post('sell', [ItemController::class, 'store']);
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'addressIndex'])->where('item_id', '[0-9]+');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'addressUpdate'])->where('item_id', '[0-9]+');
    Route::post('/item/{item_id}/comment', [ItemController::class, 'commentCreate'])->where('item_id', '[0-9]+');
    Route::post('/item/{item_id}/like', [ItemController::class, 'toggle'])->where('item_id', '[0-9]+');
    Route::get('/payment/success', [PurchaseController::class, 'success']);
    Route::get('/payment/cancel', [PurchaseController::class, 'cancel']);
});

// 認証必須 & まだ未認証ユーザーが来るページ（通知画面）
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 署名付きリンクからの検証（メールの[Verify]ボタンが叩くURL）
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mypage/profile')->with('varified', true);
})->middleware(['auth', 'signed'])->name('verification.verify');

// 検証メールの再送信
Route::get('email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return back();
    }
    $request->user()->sendEmailVerificationNotification();
    return back();
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/item/{item_id}', [ItemController::class, 'show'])->where('item_id', '[0-9]+');

Route::get('/verify-email', function () {
    return view('auth/verify-email');
});
Route::get('/', [ItemController::class, 'index']);

<?php

use App\Http\Controllers\InventoryController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/login', function () {
    return view('login');
})->name('login');


Route::get('/home', function () {
    return view('home');
})->middleware(['auth', 'verified'])->name('home');


Route::get('/register', function () {
    return view('register');
});


Route::get('/', function () {
    return view('home');
});


Route::get('/add_item', function () {
    return view('add_item');
});


Route::get('/items', function () {
    return view('items');
});


Route::get('/orders', function () {
    return view('orders');
});


Route::get('/inventory', function () {
    return view('inventory');
});


Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');


Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/login');
})->middleware(['auth', 'signed'])->name('verification.verify');


Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('resent', true);
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');




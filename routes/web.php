<?php

use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;


Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/home', function () {
    return view('home');
});

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








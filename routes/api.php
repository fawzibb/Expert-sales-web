<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/register', [UserController::class, 'store'])->name('user.store');
    Route::post('/deactivate', [AuthController::class, 'deactivateUser'])->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/items', [ItemController::class, 'index']);
        Route::post('/items', [ItemController::class, 'store']);
        Route::put('/items/{item}', [ItemController::class, 'update']);
        Route::delete('/items/{item}', [ItemController::class, 'destroy']);

        
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/orders', [OrderController::class, 'index']);  // عرض جميع الطلبات
        Route::get('/orders/{order}', [OrderController::class, 'show']);  // عرض طلب معين
        Route::post('/orders', [OrderController::class, 'store']);  // إضافة طلب جديد
        Route::put('/orders/{order}', [OrderController::class, 'update']);  // تعديل طلب
        Route::delete('/orders/{order}', [OrderController::class, 'destroy']);  // حذف طلب
    });
    Route::middleware('auth:sanctum')->get('/orders', [OrderController::class, 'index']);



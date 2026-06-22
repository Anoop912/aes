<?php


use App\Http\Controllers\CryptoController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [CryptoController::class, 'index']);

Route::post('/encrypt', [CryptoController::class, 'encrypt']);

Route::post('/decrypt', [CryptoController::class, 'decrypt']);
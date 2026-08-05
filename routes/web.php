<?php


use App\Http\Controllers\CryptoController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [CryptoController::class, 'index']);

Route::post('/encrypt', [CryptoController::class, 'encrypt']);
Route::post('/decrypt', [CryptoController::class, 'decrypt']);

Route::post('/encrypt-rsa', [CryptoController::class, 'encryptRsa']);
Route::post('/decrypt-rsa', [CryptoController::class, 'decryptRsa']);
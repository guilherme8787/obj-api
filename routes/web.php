<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::post(
    '/transacao',
    App\Http\Controllers\Transactions\TransactionController::class
)->name('transaction');

Route::post(
    '/conta',
    App\Http\Controllers\Accounts\NewAccountController::class
)->name('account.store');

Route::get(
    '/conta',
    App\Http\Controllers\Accounts\ShowAccountController::class
)->name('account.get');

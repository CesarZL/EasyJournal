<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

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

Auth::routes([
    'register' => true,
    'verify' => false, 
    'login' => true,  
    'reset' => true,
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/home', [App\Http\Controllers\HomeController::class, 'store'])->name('home.store');

Route::get('/edit', [App\Http\Controllers\EditController::class, 'index'])->name('edit');

Route::post('/home/{article}', [App\Http\Controllers\HomeController::class, 'destroy'])->name('home.destroy');
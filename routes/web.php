<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleUploadController;

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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');

    //Rutas para el controlador de articulos
    Route::get('/dashboard', [App\Http\Controllers\ArticleController::class, 'index'])->name('dashboard');
    Route::post('/articles', [App\Http\Controllers\ArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}/edit', [App\Http\Controllers\ArticleController::class, 'edit'])->name('articles.edit');
    Route::get('/articles/{article}/edit/details', [App\Http\Controllers\ArticleController::class, 'editDetails'])->name('articles.edit-details');
    Route::put('/articles/{article}', [App\Http\Controllers\ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [App\Http\Controllers\ArticleController::class, 'destroy'])->name('articles.destroy');

    //Rutas que muestra la vista upload-template
    Route::get('/templates', [App\Http\Controllers\ArticleUploadController::class, 'index'])->name('templates');
    Route::post('/templates', [App\Http\Controllers\ArticleUploadController::class, 'store'])->name('templates.store');
    Route::post('/templates/preview/{template}', [App\Http\Controllers\ArticleUploadController::class, 'preview'])->name('templates.preview');
    Route::delete('/templates/{template}', [App\Http\Controllers\ArticleUploadController::class, 'destroy'])->name('templates.destroy');

    //Rutas para el controlador de edicion de detalles de articulos
    Route::get('/articles/{article}/edit/details', [App\Http\Controllers\ArticleDetailsController::class, 'edit'])->name('articles.edit-details');
    Route::put('/articles/{article}/details', [App\Http\Controllers\ArticleDetailsController::class, 'update'])->name('articles.update-details');

    //Rutas para el controlador de edicion de coautores
    Route::get('/coauthors', [App\Http\Controllers\CoauthorController::class, 'index'])->name('coauthors');
    Route::post('/coauthors', [App\Http\Controllers\CoauthorController::class, 'store'])->name('coauthors.store');
    Route::delete('/coauthors/{coauthor}', [App\Http\Controllers\CoauthorController::class, 'destroy'])->name('coauthors.destroy');
    Route::get('coauthors/{coauthor}/edit', [App\Http\Controllers\CoauthorController::class, 'edit'])->name('coauthors.edit');
    Route::put('coauthors/{coauthor}', [App\Http\Controllers\CoauthorController::class, 'update'])->name('coauthors.update');
    
    


        

    

});

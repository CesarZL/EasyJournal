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
    
    //Rutas para el controlador de articulos
    Route::get('/dashboard', [App\Http\Controllers\ArticleController::class, 'index'])->name('dashboard');
    //ruta para crear un nuevo articulo
    Route::post('/articles', [App\Http\Controllers\ArticleController::class, 'store'])->name('articles.store');
    //ruta para mostrar la vista de edicion del articulo
    Route::get('/articles/{article}', [App\Http\Controllers\ArticleController::class, 'edit'])->name('articles.edit');
    //ruta para actualizar el articulo
    Route::put('/articles/{article}', [App\Http\Controllers\ArticleController::class, 'update'])->name('articles.update');
    //ruta para eliminar el articulo
    Route::delete('/articles/{article}', [App\Http\Controllers\ArticleController::class, 'destroy'])->name('articles.destroy');

    //Rutas para mostrar la vista de detalles del articulo y donde se deben de agregar los coautores al articulo si se desea
    Route::get('/articles/{article}/details', [App\Http\Controllers\ArticleController::class, 'edit_details'])->name('articles.edit-details');
    //Ruta para actualizar los detalles del articulo y agregar coautores
    Route::put('/articles/{article}/details', [App\Http\Controllers\ArticleController::class, 'updateDetails'])->name('articles.update-details');


    //Ruta para mostrar la vista de coautores
    Route::get('/coauthors', [App\Http\Controllers\CoauthorController::class, 'index'])->name('coauthors');
    //Ruta para almacenar un coautor
    Route::post('/coauthors', [App\Http\Controllers\CoauthorController::class, 'store'])->name('coauthors.store');
    //Ruta para eliminar un coautor
    Route::delete('/coauthors/{coauthor}', [App\Http\Controllers\CoauthorController::class, 'destroy'])->name('coauthors.destroy');
    //Ruta para mostrar la vista de edicion de un coautor
    Route::get('coauthors/{coauthor}/edit', [App\Http\Controllers\CoauthorController::class, 'edit'])->name('coauthors.edit');
    //Ruta para actualizar un coautor
    Route::put('coauthors/{coauthor}', [App\Http\Controllers\CoauthorController::class, 'update'])->name('coauthors.update');

    //Ruta para mostrar la vista de templates
    Route::get('/templates', [App\Http\Controllers\ArticleUploadController::class, 'index'])->name('templates');
    //Ruta para almacenar un template
    Route::post('/templates', [App\Http\Controllers\ArticleUploadController::class, 'store'])->name('templates.store');
    //Ruta para mostrar la vista de edicion de un template
    Route::post('/templates/preview/{template}', [App\Http\Controllers\ArticleUploadController::class, 'preview'])->name('templates.preview');
    //Ruta para eliminar un template
    Route::delete('/templates/{template}', [App\Http\Controllers\ArticleUploadController::class, 'destroy'])->name('templates.destroy');

    //Ruta para mostrar una preview de la plantilla
    Route::get('/templates/{template}/preview', [App\Http\Controllers\ArticleUploadController::class, 'showPreview'])->name('templates.show-preview');

    


        

    

});

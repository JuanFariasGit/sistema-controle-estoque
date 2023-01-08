<?php

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

Auth::routes(['verify' => true]);

Route::get('/', 'HomeController@index')->name('home');

Route::prefix('/produtos')->group(function() {
    Route::get('/', 'ProductController@index')->name('product.index');
    Route::post('/', 'ProductController@findAll');

    Route::get('/adicionar', 'ProductController@add')->name('product.add');
    Route::post('/adicionar', 'ProductController@addAction');

    Route::get('/editar/{id}', 'ProductController@edit')->name('product.edit');
    Route::post('/editar', 'ProductController@editAction')->name('product.editAction');

    Route::post('/excluir', 'ProductController@delete')->name('product.del');
    Route::post('/excluir-foto', 'ProductController@deletePhoto')->name('product.del-photo');

    Route::get('/download-photo/{id}', 'ProductController@downloadPhoto')->name('product.download-photo');
});

Route::prefix('/estoque')->group(function() {
    Route::get('/', 'StockController@index')->name('stock.index');
    Route::post('/', 'StockController@findAll');
    
    Route::post('/visualizar-movimentacao/{id}', 'StockController@viewMovement')->name('stock.view-moviment');

    Route::get('/adicionar', 'StockController@add')->name('stock.add');
    Route::post('/adicionar', 'StockController@addAction');

    Route::get('/editar/{id}', 'StockController@edit')->name('stock.edit');
    Route::post('/editar', 'StockController@editAction')->name('stock.editAction');

    Route::post('/excluir', 'StockController@delete')->name('stock.del');
});

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

Route::get('/', 'PostsController@index')->name('home');
Route::get('/posts/create', 'PostsController@create');
Route::get('/posts/{post}', 'PostsController@show');
Route::post('/posts', 'PostsController@store');

Route::post('/posts/{post}/comments', 'CommentsController@store');

Route::get('/register', 'RegistrationController@create');
Route::post('/register', 'RegistrationController@store');
Route::get('/login', 'SessionsController@create');
Route::post('/login', 'SessionsController@store');
Route::get('/logout', 'SessionsController@destroy');

Route::get('/error', 'ErrorController@index');
Route::get('/env', 'EnvironmentController@index');

Route::get('/nested/parent', 'NestedController@parent');
Route::get('/nested/child', 'NestedController@child');

Route::get('/language', 'LanguageController@index');
Route::post('/language/detect', 'LanguageController@detect');

Route::get('/books', 'BooksController@index');

Route::get('/messages', 'MessagesController@index');
Route::get('/messages/send', 'MessagesController@send');

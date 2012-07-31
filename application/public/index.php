<?php
require_once(__DIR__ . '/../../miranda/miranda.php');
use miranda\controllers\Route;

Route::get('/home(/:optional)', 'Pages#home', array('optional' => 'int'));
Route::delete("/home/:todelete", 'Pages#home');
Route::get('/about', 'Pages#about');
Route::get('/previous-about', 'Pages#about');

Route::resources('posts');

Route::post('/posts/:id/comment', 'Comments#create');
Route::get('/comments', 'Comments#index');
Route::get('/comments/:id/approve', 'Comments#approve');
Route::get('/comments/:id/decline', 'Comments#decline');

Route::other('Pages#error404');
Route::root('Pages#home');
Route::start();
?>
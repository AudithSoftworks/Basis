<?php

Route::resource('files', 'FilesController');
Route::resource('users', 'UsersController');
Route::get('/php-info', 'HomeController@phpInfo');
Route::controller('/', 'HomeController');

Route::controllers([
    'auth' => 'Users\AuthController',
    'password' => 'Users\PasswordController',
]);

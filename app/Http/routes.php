<?php

Route::resource('files', 'FilesController');
Route::resource('users', 'UsersController');

Route::controllers([
    'auth' => 'Users\AuthController',
    'password' => 'Users\PasswordController',
]);

Route::get('/php-info', 'HomeController@phpInfo');
Route::controller('/', 'HomeController');

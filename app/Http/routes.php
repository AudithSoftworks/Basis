<?php

Route::resource('files', 'FilesController');
Route::get('/php-info', 'HomeController@phpInfo');
Route::controller('/', 'HomeController');

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

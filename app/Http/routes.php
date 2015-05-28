<?php

Route::get('/php-info', function () {
    ob_start();
    phpinfo();

    return ob_get_clean();
});

Route::get('login/{provider?}', 'Users\AuthController@getLogin');
Route::post('login/{provider?}', 'Users\AuthController@postLogin');
Route::get('logout', 'Users\AuthController@getLogout');

Route::controllers([
    'password' => 'Users\PasswordController',
    'admin' => 'AdminController',
    'admin-demo' => 'Admin\DemoController'
]);

Route::resources([
    'files' => 'FilesController',
    'users' => 'UsersController'
]);

Route::controller('', 'HomeController');

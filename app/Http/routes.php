<?php

Route::get('/php-info', function () {
    ob_start();
    phpinfo();

    return ob_get_clean();
});

Route::get('auth/login/{provider?}', 'Users\AuthController@getLogin');

Route::controllers([
    'auth' => 'Users\AuthController',
    'password' => 'Users\PasswordController',
    'admin' => 'AdminController',
    'admin-demo' => 'Admin\DemoController',
    '' => 'HomeController'
]);

Route::resources([
    'files' => 'FilesController',
    'users' => 'UsersController'
]);

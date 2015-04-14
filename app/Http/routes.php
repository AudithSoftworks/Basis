<?php

Route::get('/php-info', function () {
    ob_start();
    phpinfo();

    return ob_get_clean();
});

Route::resources([
    'files' => 'FilesController',
    'users' => 'UsersController'
]);

Route::controllers([
    'auth' => 'Users\AuthController',
    'password' => 'Users\PasswordController',
    '' => 'HomeController'
]);
